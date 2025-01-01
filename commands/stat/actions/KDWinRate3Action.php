<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\actions;

use LogicException;
use Throwable;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_keys;
use function array_map;
use function fprintf;
use function fwrite;
use function implode;
use function microtime;
use function vsprintf;

use const STDERR;

final class KDWinRate3Action extends Action
{
    public function run(): int
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            throw new LogicException();
        }

        $isOk = $db->transaction(
            fn (Connection $db): bool => $this->makeStats($db),
            Transaction::REPEATABLE_READ,
        );
        if (!$isOk) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->vacuumTables($db);

        return ExitCode::OK;
    }

    private function makeStats(Connection $db): bool
    {
        $transaction = $db->transaction;
        if (!$transaction) {
            throw new LogicException();
        }

        fwrite(STDERR, "Updating stat_kd_win_rate3\n");
        $t1 = microtime(true);
        try {
            if (!$db->createCommand($this->makeSQL($db))->execute()) {
                $transaction->rollBack();
                return false;
            }
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage() . "\n");

            $transaction->rollBack();
            return false;
        } finally {
            $t2 = microtime(true);
            fprintf(STDERR, "Took %.3f sec\n", $t2 - $t1);
        }

        return true;
    }

    private function makeSQL(Connection $db): string
    {
        $private = Lobby3::find()->andWhere(['key' => 'private'])->limit(1)->one($db);
        $tricolor = Rule3::find()->andWhere(['key' => 'tricolor'])->limit(1)->one($db);
        if (!$private || !$tricolor) {
            throw new LogicException();
        }

        $select = (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'kills' => self::limitN('{{%battle_player3}}.[[kill]]'),
                'deaths' => self::limitN('{{%battle_player3}}.[[death]]'),
                'battles' => 'COUNT(*)',
                'wins' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                        'ELSE 0',
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[lobby_id]]' => null]],
                ['not', ['{{%battle3}}.[[lobby_id]]' => $private->id]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => $tricolor->id]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
            ])
            ->groupBy([
                '{{%season3}}.[[id]]',
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle3}}.[[rule_id]]',
                self::limitN('{{%battle_player3}}.[[kill]]'),
                self::limitN('{{%battle_player3}}.[[death]]'),
            ]);

        return vsprintf('INSERT INTO %s ( %s ) %s ON CONFLICT ( %s ) DO UPDATE SET %s', [
            $db->quoteTableName('{{%stat_kd_win_rate3}}'),
            self::columnList($db, array_keys($select->select)),
            $select->createCommand($db)->rawSql,
            self::columnList($db, ['season_id', 'lobby_id', 'rule_id', 'kills', 'deaths']),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => vsprintf('%2$s = %1$s.%2$s', [
                        $db->quoteTableName('excluded'),
                        $db->quoteColumnName($columnName),
                    ]),
                    ['battles', 'wins'],
                ),
            ),
        ]);
    }

    /**
     * @param string[] $list
     */
    private static function columnList(Connection $db, array $list): string
    {
        return implode(
            ', ',
            array_map(
                fn (string $column): string => $db->quoteColumnName($column),
                $list,
            ),
        );
    }

    private static function limitN(string $column, int $limit = 20): string
    {
        return vsprintf('(CASE WHEN %1$s >= %2$d THEN %2$d ELSE %1$s END)', [
            $column,
            $limit,
        ]);
    }

    private function vacuumTables(Connection $db): void
    {
        $tables = [
            '{{%stat_kd_win_rate3}}',
        ];

        foreach ($tables as $table) {
            fprintf(STDERR, "Vacuuming %s\n", $table);
            $sql = vsprintf('VACUUM ( ANALYZE ) %s', [
                $db->quoteTableName($table),
            ]);
            $db->createCommand($sql)->execute();
        }

        fwrite(STDERR, "OK\n");
    }
}
