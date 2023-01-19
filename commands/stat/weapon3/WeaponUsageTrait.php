<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatWeapon3Usage;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function fwrite;
use function implode;
use function sprintf;
use function vsprintf;

use const SORT_ASC;
use const STDERR;

trait WeaponUsageTrait
{
    protected function makeStatWeapon3Usage(): void
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            return;
        }

        fwrite(STDERR, "Updating stat_weapon3_usage...\n");
        $db->transaction(
            fn (Connection $db) => $this->makeStatWeapon3UsageImpl($db),
            Transaction::REPEATABLE_READ,
        );

        fwrite(STDERR, "Vacuuming stat_weapon3_usage\n");
        $db->createCommand('VACUUM ( ANALYZE ) {{%stat_weapon3_usage}}')->execute();
        fwrite(STDERR, "Update done\n");
    }

    private function makeStatWeapon3UsageImpl(Connection $db): void
    {
        $targetSeasons = $this->getStatWeapon3UsageTargetSeasons($db);
        StatWeapon3Usage::deleteAll([
            'season_id' => ArrayHelper::getColumn($targetSeasons, 'id'),
        ]);

        $select = $this->buildSelectForWeapon3Usage($db, $targetSeasons);
        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName('{{%stat_weapon3_usage}}'),
            implode(
                ', ',
                array_map(
                    fn (string $columnName): string => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute($db);
    }

    /**
     * @return Season3[]
     */
    private function getStatWeapon3UsageTargetSeasons(Connection $db): array
    {
        $date = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->sub(new DateInterval('P30D'));

        $minModel = Season3::find()
            ->andWhere(
                vsprintf('[[term]] @> %s::TIMESTAMPTZ', [
                    $db->quoteValue($date->format(DateTimeInterface::ATOM)),
                ]),
            )
            ->limit(1)
            ->one($db);
        if (!$minModel) {
            throw new Exception();
        }

        return Season3::find()
            ->andWhere(['>=', 'start_at', $minModel->start_at])
            ->orderBy(['start_at' => SORT_ASC])
            ->all($db);
    }

    /**
     * @param Season3[] $seasons
     */
    private function buildSelectForWeapon3Usage(Connection $db, array $seasons): Query
    {
        $lobbies = ArrayHelper::map(
            Lobby3::find()->all($db),
            'key',
            'id',
        );

        $tricolor = Rule3::find()->andWhere(['key' => 'tricolor'])->limit(1)->one();
        if (!$tricolor) {
            throw new Exception();
        }

        $selectPkey = [
            'season_id' => '{{%season3}}.[[id]]',
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
        ];

        return (new Query())
            ->select(array_merge(
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
                    'seconds' => vsprintf('SUM(%s)', [
                        vsprintf('%s - %s', [
                            'EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]])',
                            'EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]])',
                        ]),
                    ]),
                ],
                $this->buildSelectMetricsForWeapon3Usage('kill', '{{%battle_player3}}.[[kill]]'),
                $this->buildSelectMetricsForWeapon3Usage('assist', '{{%battle_player3}}.[[assist]]'),
                $this->buildSelectMetricsForWeapon3Usage('death', '{{%battle_player3}}.[[death]]'),
                $this->buildSelectMetricsForWeapon3Usage('special', '{{%battle_player3}}.[[special]]'),
                $this->buildSelectMetricsForWeapon3Usage('inked', '{{%battle_player3}}.[[inked]]', false),
            ))
            ->from('{{%battle3}}')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => [
                        $lobbies['regular'],
                        $lobbies['splatfest_challenge'],
                        $lobbies['xmatch'],
                    ],
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%battle_player3}}.[[is_disconnected]]' => false,
                    '{{%battle_player3}}.[[is_me]]' => false,
                    '{{%result3}}.[[aggregatable]]' => true,
                    '{{%season3}}.[[id]]' => ArrayHelper::getColumn($seasons, 'id'),
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

    private function buildSelectMetricsForWeapon3Usage(string $baseName, string $column, bool $mode = true): array
    {
        $percentile = fn (string $column, int $percentile): string => sprintf(
            'PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s)',
            $percentile / 100.0,
            $column,
        );

        $columns = [
            "avg_{$baseName}" => "AVG({$column})",
            "sd_{$baseName}" => "STDDEV_SAMP({$column})",
            "min_{$baseName}" => "MIN({$column})",
            "p05_{$baseName}" => $percentile($column, 5),
            "p25_{$baseName}" => $percentile($column, 25),
            "p50_{$baseName}" => $percentile($column, 50),
            "p75_{$baseName}" => $percentile($column, 75),
            "p95_{$baseName}" => $percentile($column, 95),
            "max_{$baseName}" => "MAX({$column})",
        ];

        return array_merge(
            $columns,
            $mode ? ["mode_{$baseName}" => "MODE() WITHIN GROUP (ORDER BY {$column})"] : [],
        );
    }
}
