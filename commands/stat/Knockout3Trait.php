<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_keys;
use function array_map;
use function fwrite;
use function implode;
use function vsprintf;

use const STDERR;

trait Knockout3Trait
{
    protected function updateKnockout3(): void
    {
        fwrite(STDERR, "Updating knockout3...\n");
        Yii::$app->db->transaction(
            function (Connection $db): void {
                $select = $this->buildBasicSelectForKnockout3();
                $this->updateKnockout3stats($db, $select);

                $select->select['map_id'] = '(NULL)';
                $select->groupBy(['{{%season3}}.[[id]]', '{{%battle3}}.[[rule_id]]']);
                $this->updateKnockout3stats($db, $select);
            },
            Transaction::READ_COMMITTED,
        );
        fwrite(STDERR, "Updated knockout3.\n");
    }

    private function buildBasicSelectForKnockout3(): Query
    {
        $time = '(EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]]) - EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]]))';
        $koTime = "(CASE WHEN {{%battle3}}.[[is_knockout]] THEN {$time} ELSE NULL END)";
        return (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'map_id' => '{{%battle3}}.[[map_id]]',
                'battles' => 'COUNT(*)',
                'knockout' => 'SUM(CASE WHEN {{%battle3}}.[[is_knockout]] THEN 1 ELSE 0 END)',
                'avg_battle_time' => "AVG({$time})",
                'stddev_battle_time' => "STDDEV_SAMP({$time})",
                'avg_knockout_time' => "AVG({$koTime})",
                'stddev_knockout_time' => "STDDEV_SAMP({$koTime})",
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
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
            ->groupBy([
                '{{%season3}}.[[id]]',
                '{{%battle3}}.[[rule_id]]',
                '{{%battle3}}.[[map_id]]',
            ]);
    }

    private function updateKnockout3stats(Connection $db, Query $select): void
    {
        $sql = vsprintf('INSERT INTO %s (%s) %s ON CONFLICT %s %s', [
            $db->quoteTableName('{{%knockout3}}'),
            implode(
                ', ',
                array_map(
                    fn (string $columnName) => $db->quoteColumnName($columnName),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
            // conflict_target
            vsprintf('(%s)', [
                implode(', ', [
                    '[[season_id]]',
                    '[[rule_id]]',
                    'COALESCE([[map_id]], 0)',
                ]),
            ]),
            vsprintf('DO UPDATE SET %s', [
                implode(
                    ', ',
                    array_map(
                        fn (string $name): string => vsprintf('%1$s = {{excluded}}.%1$s', [
                            $db->quoteColumnName($name),
                        ]),
                        [
                            'battles',
                            'knockout',
                            'avg_battle_time',
                            'stddev_battle_time',
                            'avg_knockout_time',
                            'stddev_knockout_time',
                        ],
                    ),
                ),
            ]),
        ]);
        $db->createCommand($sql)->execute();
    }
}
