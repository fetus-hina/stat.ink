<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\actions;

use Exception;
use LogicException;
use Yii;
use app\models\StatInkColor3;
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
use function vsprintf;

use const STDERR;

final class InkColor3Action extends Action
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

        fwrite(STDERR, "Updating stat_ink_color3\n");

        StatInkColor3::deleteAll('1 = 1');

        $color1 = 'LEAST({{%battle3}}.[[our_team_color]], {{%battle3}}.[[their_team_color]])';
        $color2 = 'GREATEST({{%battle3}}.[[our_team_color]], {{%battle3}}.[[their_team_color]])';
        $select = (new Query())
            ->select([
                'color1' => $color1,
                'color2' => $color2,
                'battles' => 'COUNT(*)',
                'wins' => vsprintf('SUM(%s)', [
                    vsprintf('CASE WHEN %s THEN 1 ELSE 0 END', [
                        "{{%result3}}.[[is_win]] = ($color1 = {{%battle3}}.[[our_team_color]])",
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['<>', '{{%rule3}}.[[key]]', 'tricolor'],
                ['not', ['{{%battle3}}.[[our_team_color]]' => null]],
                ['not', ['{{%battle3}}.[[their_team_color]]' => null]],
                ['not', ['{{%lobby3}}.[[key]]' => ['private', 'splatfest_challenge', 'splatfest_open']]],
                '{{%battle3}}.[[our_team_color]] <> {{%battle3}}.[[their_team_color]]',
            ])
            ->groupBy([$color1, $color2]);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            $db->quoteTableName('{{%stat_ink_color3}}'),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        if (!$db->createCommand($sql)->execute()) {
            throw new Exception('Failed to update');
        }

        return true;
    }

    private function vacuumTables(Connection $db): void
    {
        $tables = [
            '{{%stat_ink_color3}}',
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
