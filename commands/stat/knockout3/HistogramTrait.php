<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\knockout3;

use yii\db\Connection;
use yii\db\Query;

use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function fwrite;
use function implode;
use function sprintf;
use function vsprintf;

use const STDERR;

trait HistogramTrait
{
    private function updateKnockout3Histogram(Connection $db): void
    {
        fwrite(STDERR, "Cleaning up knockout3_histogram...\n");
        $db->createCommand('DELETE FROM {{%knockout3_histogram}}')->execute();

        fwrite(STDERR, "Updating knockout3_histogram (stages)...\n");
        $this->updateKnockout3HistogramStats(
            $db,
            $this->buildHistogramSelectForKnockout3(enableMap: true),
        );

        fwrite(STDERR, "Updating knockout3_histogram (total)...\n");
        $this->updateKnockout3HistogramStats(
            $db,
            $this->buildHistogramSelectForKnockout3(enableMap: false),
        );
    }

    private function buildHistogramSelectForKnockout3(bool $enableMap = true): Query
    {
        $time = '(EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]]) - EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]]))';
        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s / %2$s.%3$s) + 0.5) * %2$s.%3$s)::integer',
            $time,
            '{{%knockout3}}',
            '[[histogram_width]]',
        );

        return (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'map_id' => $enableMap ? '{{%battle3}}.[[map_id]]' : '(NULL)',
                'class_value' => $classValue,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin('{{%map3}}', '{{%battle3}}.[[map_id]] = {{%map3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin('{{%knockout3}}', implode(' AND ', [
                '{{%knockout3}}.[[season_id]] = {{%season3}}.[[id]]',
                '{{%knockout3}}.[[rule_id]] = {{%battle3}}.[[rule_id]]',
                $enableMap
                    ? '{{%knockout3}}.[[map_id]] = {{%battle3}}.[[map_id]]'
                    : '{{%knockout3}}.[[map_id]] IS NULL',
            ]))
            ->andWhere(['and',
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[is_knockout]]' => null]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%lobby3}}.[[key]]' => 'xmatch',
                    '{{%result3}}.[[aggregatable]]' => true,
                    '{{%rule3}}.[[key]]' => ['area', 'yagura', 'hoko', 'asari'],
                ],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy(
                array_values(
                    array_filter(
                        [
                            '{{%season3}}.[[id]]',
                            '{{%battle3}}.[[rule_id]]',
                            $enableMap ? '{{%battle3}}.[[map_id]]' : null,
                            $classValue,
                        ],
                        fn (?string $value): bool => $value !== null,
                    ),
                ),
            );
    }

    private function updateKnockout3HistogramStats(Connection $db, Query $select): void
    {
        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            $db->quoteTableName('{{%knockout3_histogram}}'),
            implode(
                ', ',
                array_map(
                    fn (string $columnName) => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);

        $db->createCommand($sql)->execute();
    }
}
