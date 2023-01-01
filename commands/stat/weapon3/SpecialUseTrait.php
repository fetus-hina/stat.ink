<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_filter;
use function array_keys;
use function array_map;
use function fwrite;
use function implode;
use function sprintf;
use function vsprintf;

use const STDERR;

trait SpecialUseTrait
{
    protected function makeStatWeapon3SpecialUse(): void
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            return;
        }

        fwrite(STDERR, "Updating stat_special_use3...\n");
        $db->transaction(
            function (Connection $db): void {
                foreach ([false, true] as $aggRule) {
                    $select = $this->buildSelectForWeapon3SpecialUse(aggRule: $aggRule);
                    $sql = vsprintf('INSERT INTO %s ( %s ) %s ON CONFLICT ( %s ) DO UPDATE SET %s', [
                        $db->quoteTableName('{{%stat_special_use3}}'),
                        implode(', ', array_map(
                            fn (string $columnName): string => $db->quoteColumnName($columnName),
                            array_keys($select->select),
                        )),
                        $select->createCommand($db)->rawSql,
                        implode(', ', [
                            '[[season_id]]',
                            'COALESCE([[rule_id]], 0)',
                            '[[special_id]]',
                        ]),
                        implode(', ', array_map(
                            fn (string $columnName): string => vsprintf('%1$s = {{excluded}}.%1$s', [
                                $db->quoteColumnName($columnName),
                            ]),
                            [
                                'sample_size',
                                'win',
                                'avg_uses',
                                'stddev',
                                'percentile_5',
                                'percentile_25',
                                'percentile_50',
                                'percentile_75',
                                'percentile_95',
                                'percentile_100',
                            ],
                        )),
                    ]);

                    $db->createCommand($sql)->execute();
                }
            },
            Transaction::REPEATABLE_READ,
        );
        fwrite(STDERR, "Vacuuming stat_special_use3\n");
        $db->createCommand('VACUUM ( ANALYZE ) {{%stat_special_use3}}')->execute();
        fwrite(STDERR, "Update done\n");
    }

    private function buildSelectForWeapon3SpecialUse(bool $aggRule): Query
    {
        $percentile = fn (int $pct): string => sprintf(
            'PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY {{bp}}.[[special]] ASC)',
            $pct / 100,
        );

        return (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'rule_id' => $aggRule ? '{{%battle3}}.[[rule_id]]' : '(NULL)',
                'special_id' => '{{%weapon3}}.[[special_id]]',
                'sample_size' => 'COUNT(*)',
                'win' => 'SUM(CASE WHEN {{%result3}}.[[is_win]] = {{bp}}.[[is_our_team]] THEN 1 ELSE 0 END)',
                'avg_uses' => 'AVG({{bp}}.[[special]])',
                'stddev' => 'STDDEV_SAMP({{bp}}.[[special]])',
                'percentile_5' => $percentile(5),
                'percentile_25' => $percentile(25),
                'percentile_50' => $percentile(50),
                'percentile_75' => $percentile(75),
                'percentile_95' => $percentile(95),
                'percentile_100' => 'MAX({{bp}}.[[special]])',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin(['bp' => '{{%battle_player3}}'], '{{%battle3}}.[[id]] = {{bp}}.[[battle_id]]')
            ->innerJoin('{{%weapon3}}', '{{bp}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
            ->innerJoin('{{%special3}}', '{{%weapon3}}.[[special_id]] = {{%special3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%lobby3}}.[[key]]' => 'xmatch',
                    '{{%result3}}.[[aggregatable]]' => true,
                    '{{bp}}.[[is_me]]' => false,
                ],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%weapon3}}.[[special_id]]' => null]],
                ['not', ['{{bp}}.[[special]]' => null]],
            ])
            ->groupBy(
                array_filter([
                    '{{%season3}}.[[id]]',
                    $aggRule ? '{{%battle3}}.[[rule_id]]' : null,
                    '{{%weapon3}}.[[special_id]]',
                ]),
            );
    }
}
