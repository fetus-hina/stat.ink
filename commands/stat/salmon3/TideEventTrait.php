<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat\salmon3;

use Yii;
use app\models\StatSalmon3TideEvent;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_map;
use function assert;
use function implode;
use function vsprintf;

trait TideEventTrait
{
    protected function makeStatSalmon3TideEvent(): void
    {
        $db = Yii::$app->db;
        assert($db instanceof Connection);

        $db->transaction(
            function (Connection $db): void {
                StatSalmon3TideEvent::deleteAll();
                $db
                    ->createCommand(vsprintf('INSERT INTO %s (%s) %s', [
                        $db->quoteTableName('{{%stat_salmon3_tide_event}}'),
                        implode(
                            ', ',
                            array_map(
                                fn (string $columnName): string => $db->quoteColumnName($columnName),
                                ['stage_id', 'big_stage_id', 'tide_id', 'event_id', 'jobs', 'cleared'],
                            ),
                        ),
                        $this->getSelectForStatSalmon3TideEvent($db),
                    ]))
                    ->execute();
            },
            Transaction::READ_COMMITTED,
        );

        $db->createCommand('VACUUM ( ANALYZE ) {{%stat_salmon3_tide_event}}')->execute();
    }

    private function getSelectForStatSalmon3TideEvent(Connection $db): string
    {
        return (new Query())
            ->select([
                'stage_id' => '{{%salmon3}}.[[stage_id]]',
                'big_stage_id' => '{{%salmon3}}.[[big_stage_id]]',
                'tide_id' => '{{%salmon_wave3}}.[[tide_id]]',
                'event_id' => '{{%salmon_wave3}}.[[event_id]]',
                'jobs' => 'COUNT(*)',
                'cleared' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon3}}.[[clear_waves]] >= {{%salmon_wave3}}.[[wave]] THEN 1',
                        'ELSE 0',
                    ]),
                ]),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_wave3}}', '{{%salmon3}}.[[id]] = {{%salmon_wave3}}.[[salmon_id]]')
            ->andWhere([
                '{{%salmon3}}.[[has_broken_data]]' => false,
                '{{%salmon3}}.[[has_disconnect]]' => false,
                '{{%salmon3}}.[[is_automated]]' => true,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[is_eggstra_work]]' => false,
                '{{%salmon3}}.[[is_private]]' => false,
            ])
            ->andWhere(['not', ['{{%salmon_wave3}}.[[tide_id]]' => null]])
            ->andWhere(['BETWEEN', '{{%salmon3}}.[[clear_waves]]', 0, 3])
            ->andWhere(['or',
                ['and',
                    '{{%salmon3}}.[[stage_id]] IS NOT NULL',
                    '{{%salmon3}}.[[big_stage_id]] IS NULL',
                ],
                ['and',
                    '{{%salmon3}}.[[stage_id]] IS NULL',
                    '{{%salmon3}}.[[big_stage_id]] IS NOT NULL',
                ],
            ])
            ->groupBy([
                '{{%salmon3}}.[[stage_id]]',
                '{{%salmon3}}.[[big_stage_id]]',
                '{{%salmon_wave3}}.[[tide_id]]',
                '{{%salmon_wave3}}.[[event_id]]',
            ])
            ->createCommand($db)
            ->rawSql;
    }
}
