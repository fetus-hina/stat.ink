<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use Yii;
use app\models\StatSpecialUseCount3;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_keys;
use function array_map;
use function fwrite;
use function implode;
use function vsprintf;

use const STDERR;

trait SpecialUseCountTrait
{
    protected function makeStatWeapon3SpecialUseCount(): void
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            return;
        }

        fwrite(STDERR, "Updating stat_special_use_count3...\n");
        $db->transaction(
            function (Connection $db): void {
                StatSpecialUseCount3::deleteAll('1 = 1');

                $select = $this->buildSelectForWeapon3SpecialUseCount();
                $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
                    $db->quoteTableName('{{%stat_special_use_count3}}'),
                    implode(', ', array_map(
                        fn (string $columnName): string => $db->quoteColumnName($columnName),
                        array_keys($select->select),
                    )),
                    $select->createCommand($db)->rawSql,
                ]);
                $db->createCommand($sql)->execute();
            },
            Transaction::REPEATABLE_READ,
        );

        fwrite(STDERR, "Vacuuming stat_special_use_count3\n");
        $db->createCommand('VACUUM ( ANALYZE ) {{%stat_special_use_count3}}')->execute();
        fwrite(STDERR, "Update done\n");
    }

    private function buildSelectForWeapon3SpecialUseCount(): Query
    {
        $condTurfWar = [
            '{{%rule3}}.[[key]]' => 'nawabari',
            '{{%lobby3}}.[[key]]' => ['regular', 'splatfest_challenge'],
        ];

        $condGachi = [
            '{{%rule_group3}}.[[key]]' => 'gachi',
            '{{%lobby3}}.[[key]]' => 'xmatch',
        ];

        return (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'special_id' => '{{%weapon3}}.[[special_id]]',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'use_count' => '{{%battle_player3}}.[[special]]',
                'sample_size' => 'COUNT(*)',
                'win' => vsprintf('SUM(%s)', [
                    vsprintf('CASE %s END', [
                        implode(' ', [
                            'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                            'ELSE 0',
                        ]),
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->innerJoin('{{%weapon3}}', '{{%battle_player3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%map3}}', '{{%battle3}}.[[map_id]] = {{%map3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin('{{%rule_group3}}', '{{%rule3}}.[[group_id]] = {{%rule_group3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%weapon3}}.[[special_id]]' => null]],
                ['or',
                    $condTurfWar,
                    $condGachi,
                ],
            ])
            ->groupBy([
                '{{%season3}}.[[id]]',
                '{{%weapon3}}.[[special_id]]',
                '{{%battle3}}.[[rule_id]]',
                '{{%battle_player3}}.[[special]]',
            ]);
    }
}
