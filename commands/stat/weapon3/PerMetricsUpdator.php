<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use Exception;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\StatWeapon3Assist;
use app\models\StatWeapon3AssistPerVersion;
use app\models\StatWeapon3Death;
use app\models\StatWeapon3DeathPerVersion;
use app\models\StatWeapon3Inked;
use app\models\StatWeapon3InkedPerVersion;
use app\models\StatWeapon3Kill;
use app\models\StatWeapon3KillOrAssist;
use app\models\StatWeapon3KillOrAssistPerVersion;
use app\models\StatWeapon3KillPerVersion;
use app\models\StatWeapon3Special;
use app\models\StatWeapon3SpecialPerVersion;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function fwrite;
use function implode;
use function vsprintf;

use const STDERR;

final class PerMetricsUpdator
{
    public static function update(): void
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            return;
        }

        $db->transaction(
            fn (Connection $db) => self::doUpdate($db),
            Transaction::REPEATABLE_READ,
        );

        self::vacuumTables($db);

        fwrite(STDERR, "Done.\n");
    }

    private static function doUpdate(Connection $db): void
    {
        $attribute = [
            'assist' => '{{%battle_player3}}.[[assist]]',
            'death' => '{{%battle_player3}}.[[death]]',
            'inked' => '({{%battle_player3}}.[[inked]] / 25 * 25)', // "整数 / 整数" は切り捨てられる
            'kill' => '{{%battle_player3}}.[[kill]]',
            'kill_or_assist' => '{{%battle_player3}}.[[kill_or_assist]]',
            'special' => '{{%battle_player3}}.[[special]]',
        ];

        foreach ($attribute as $attr => $sqlAttr) {
            self::updateSeasonMetrics($db, $attr, $sqlAttr);
            self::updateVersionMetrics($db, $attr, $sqlAttr);
        }
    }

    private static function updateSeasonMetrics(Connection $db, string $attr, string $sqlAttr): void
    {
        $select = self::createSelectForUpdate($db, 'season', $attr, $sqlAttr);
        $tableName = "{{%stat_weapon3_{$attr}}}";
        fwrite(STDERR, "Updating {$tableName}...\n");

        $db->createCommand()->delete($tableName, '1 = 1')->execute();

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $tableName,
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();
    }

    private static function updateVersionMetrics(Connection $db, string $attr, string $sqlAttr): void
    {
        $select = self::createSelectForUpdate($db, 'version', $attr, $sqlAttr);
        $tableName = "{{%stat_weapon3_{$attr}_per_version}}";
        fwrite(STDERR, "Updating {$tableName}...\n");

        $db->createCommand()->delete($tableName, '1 = 1')->execute();

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $tableName,
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();
    }

    /**
     * @param 'season'|'version' $period
     */
    private static function createSelectForUpdate(
        Connection $db,
        string $period,
        string $attr,
        string $sqlAttr,
    ): Query {
        $lobbies = ArrayHelper::map(
            Lobby3::find()->all($db),
            'key',
            'id',
        );

        $tricolor = Rule3::find()
            ->andWhere(['key' => 'tricolor'])
            ->limit(1)
            ->one($db);
        if (!$tricolor) {
            throw new Exception();
        }

        $selectPkey = array_filter(
            [
                'season_id' => $period === 'season' ? '{{%season3}}.[[id]]' : null,
                'version_id' => $period === 'version' ? '{{%battle3}}.[[version_id]]' : null,
                'lobby_id' => vsprintf('(CASE %s END)', [
                    implode(' ', [
                        vsprintf('WHEN {{%%battle3}}.[[lobby_id]] = %d THEN %d', [
                            $lobbies['splatfest_challenge'],
                            $lobbies['regular'],
                        ]),
                        'ELSE {{%battle3}}.[[lobby_id]]',
                    ]),
                ]),
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
                $attr => $sqlAttr,
            ],
            fn (mixed $v): bool => $v !== null,
        );

        return (new Query())
            ->select(
                array_merge(
                    $selectPkey,
                    [
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(%s)', [
                            vsprintf('CASE %s END', [
                                implode(' ', [
                                    'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                                    'ELSE 0',
                                ]),
                            ]),
                        ]),
                    ],
                ),
            )
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => [
                        $lobbies['bankara_challenge'],
                        $lobbies['regular'],
                        $lobbies['splatfest_challenge'],
                        $lobbies['xmatch'],
                    ],
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => $tricolor->id]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['not', ['{{%battle_player3}}.[[assist]]' => null]],
                ['not', ['{{%battle_player3}}.[[death]]' => null]],
                ['not', ['{{%battle_player3}}.[[inked]]' => null]],
                ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                ['not', ['{{%battle_player3}}.[[special]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy(array_values($selectPkey));
    }

    private static function vacuumTables(Connection $db): void
    {
        fwrite(STDERR, "Vacuuming tables...\n");

        $sql = vsprintf('VACUUM ( ANALYZE ) %s', [
            implode(
                ', ',
                array_map(
                    fn (string $tableName): string => $db->quoteTableName($tableName),
                    [
                        StatWeapon3Assist::tableName(),
                        StatWeapon3AssistPerVersion::tableName(),
                        StatWeapon3Death::tableName(),
                        StatWeapon3DeathPerVersion::tableName(),
                        StatWeapon3Inked::tableName(),
                        StatWeapon3InkedPerVersion::tableName(),
                        StatWeapon3Kill::tableName(),
                        StatWeapon3KillOrAssist::tableName(),
                        StatWeapon3KillOrAssistPerVersion::tableName(),
                        StatWeapon3KillPerVersion::tableName(),
                        StatWeapon3Special::tableName(),
                        StatWeapon3SpecialPerVersion::tableName(),
                    ],
                ),
            ),
        ]);

        $db->createCommand($sql)->execute();
    }
}
