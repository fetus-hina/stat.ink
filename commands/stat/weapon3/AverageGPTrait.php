<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\weapon3;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use app\models\Ability3;
use app\models\Season3;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_keys;
use function array_map;
use function fwrite;
use function implode;
use function in_array;
use function min;
use function sprintf;
use function vsprintf;

use const SORT_ASC;
use const STDERR;

trait AverageGPTrait
{
    protected function makeStatAverageGPs(): void
    {
        $yesterday = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setTime(0, 0, 0) // now, 00:00:00 UTC of today
            ->sub(new DateInterval('PT1S')); // final second of yesterday

        $threshold = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setTime(0, 0, 0) // now, 00:00:00 UTC of today
            ->sub(new DateInterval('P3D')) // 3 days ago
            ->sub(new DateInterval('PT1S')); // final second of 3 days ago

        $targetSeasons = Season3::find()
            ->andWhere(['or',
                ['@>', 'term',
                    new Expression(
                        vsprintf('%s::TIMESTAMP(0) WITH TIME ZONE', [
                            Yii::$app->db->quoteValue($yesterday->format(DateTimeInterface::ATOM)),
                        ]),
                    ),
                ],
                ['@>', 'term',
                    new Expression(
                        vsprintf('%s::TIMESTAMP(0) WITH TIME ZONE', [
                            Yii::$app->db->quoteValue($threshold->format(DateTimeInterface::ATOM)),
                        ]),
                    ),
                ],
            ])
            ->all();

        fwrite(STDERR, "Average GPs calculation start...\n");
        Yii::$app->db->transaction(
            fn (Connection $db) => $this->makeStatAverageGPsImpl($db, $targetSeasons, $yesterday),
            Transaction::REPEATABLE_READ,
        );
        fwrite(STDERR, "Average GPs calculation done\n");
    }

    /**
     * @param Season3[] $targetSeasons
     */
    private function makeStatAverageGPsImpl(
        Connection $db,
        array $targetSeasons,
        DateTimeInterface $yesterday,
    ): void {
        $abilities = ArrayHelper::map(
            Ability3::find()
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
            'key',
            'id',
        );

        $columns = [
            'season_id' => '{{%season3}}.[[id]]',
            'rule_id' => '{{%battle3}}.[[rule_id]]',
            'range_id' => '{{%stat_weapon3_x_usage_range}}.[[id]]',
            'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
            'players' => 'COUNT(*)',
        ];

        $select = (new Query())
            ->from('{{%battle3}}')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin(
                '{{%stat_weapon3_x_usage_range}}',
                '{{%battle3}}.[[x_power_before]] <@ {{%stat_weapon3_x_usage_range}}.[[x_power_range]]',
            )
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%rule3}}.[[key]]' => ['area', 'yagura', 'hoko', 'asari'],
                    '{{%lobby3}}.[[key]]' => 'xmatch',
                    '{{%season3}}.[[id]]' => ArrayHelper::getColumn($targetSeasons, 'id'),
                ],
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['not', ['{{%battle3}}.[[x_power_before]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
                ['between', '{{%battle3}}.[[start_at]]',
                    min(ArrayHelper::getColumn($targetSeasons, 'start_at')),
                    $yesterday->format(DateTimeInterface::ATOM),
                ],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy([
                $columns['season_id'],
                $columns['rule_id'],
                $columns['range_id'],
                $columns['weapon_id'],
            ]);

        foreach ($abilities as $key => $id) {
            $tmpTable = "pa_{$key}";
            $select->leftJoin(
                [$tmpTable => '{{%battle_player_gear_power3}}'],
                ['and',
                    "{{%battle_player3}}.[[id]] = {{{$tmpTable}}}.[[player_id]]",
                    "{{{$tmpTable}}}.[[ability_id]] = {$id}",
                ],
            );
            $value = vsprintf('(CASE WHEN %s THEN %s ELSE 0 END)', [
                sprintf('{{%s}}.[[id]] IS NOT NULL', $tmpTable),
                sprintf('{{%s}}.[[gear_power]]', $tmpTable),
            ]);

            $columns["{$key}_players"] = "SUM(CASE WHEN {{{$tmpTable}}}.[[id]] IS NOT NULL THEN 1 ELSE 0 END)";
            $columns["{$key}_avg"] = "AVG({$value})";
        }

        $select->select($columns);

        $primaryKey = ['season_id', 'rule_id', 'range_id', 'weapon_id'];
        $sql = vsprintf('INSERT INTO %s (%s) %s ON CONFLICT (%s) DO UPDATE SET %s', [
            $db->quoteTableName('{{%stat_ability3_x_usage}}'),
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($columns),
                ),
            ),
            $select->createCommand($db)->rawSql,
            implode(', ', array_map($db->quoteColumnName(...), $primaryKey)),
            implode(
                ', ',
                array_filter(
                    array_map(
                        fn (string $name): ?string => in_array($name, $primaryKey, true)
                            ? null
                            : vsprintf('%1$s = EXCLUDED.%1$s', [
                                $db->quoteColumnName($name),
                            ]),
                        array_keys($columns),
                    ),
                ),
            ),
        ]);

        $db->createCommand($sql)->execute();
    }
}
