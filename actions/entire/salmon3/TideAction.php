<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\salmon3;

use Yii;
use app\models\Map3;
use app\models\SalmonEvent3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

final class TideAction extends Action
{
    public function run(): string
    {
        return Yii::$app->db->transaction(
            fn (Connection $db): string => $this->controller->render('salmon3/tide', [
                'bigMaps' => $this->getBigMaps($db),
                'events' => $this->getEvents($db),
                'mapTides' => $this->getMapTides($db),
                'maps' => $this->getMaps($db),
                'tides' => $this->getTides($db),
            ]),
            Transaction::READ_COMMITTED,
        );
    }

    private function getBigMaps(Connection $db): array
    {
        return ArrayHelper::map(
            Map3::find()->orderBy(['name' => SORT_ASC])->all(),
            'id',
            fn (Map3 $v): Map3 => $v,
        );
    }

    private function getMapTides(Connection $db): array
    {
        $data = (new Query())
            ->select([
                'stage_id' => '{{%stat_salmon3_tide_event}}.[[stage_id]]',
                'big_stage_id' => '{{%stat_salmon3_tide_event}}.[[big_stage_id]]',
                'tide_id' => '{{%stat_salmon3_tide_event}}.[[tide_id]]',
                'jobs' => 'SUM({{%stat_salmon3_tide_event}}.[[jobs]])',
                'cleared' => 'SUM({{%stat_salmon3_tide_event}}.[[cleared]])',
            ])
            ->from('{{%stat_salmon3_tide_event}}')
            ->groupBy([
                '{{%stat_salmon3_tide_event}}.[[stage_id]]',
                '{{%stat_salmon3_tide_event}}.[[big_stage_id]]',
                '{{%stat_salmon3_tide_event}}.[[tide_id]]',
            ])
            ->orderBy([
                'stage_id' => SORT_ASC,
                'big_stage_id' => SORT_ASC,
                'tide_id' => SORT_ASC,
            ])
            ->all($db);

        $results = [];
        foreach ($data as $row) {
            $tmpId = sprintf('%d-%d', (int)$row['stage_id'], (int)$row['big_stage_id']);
            if (!isset($results[$tmpId])) {
                $results[$tmpId] = [
                    'stage_id' => $row['stage_id'],
                    'big_stage_id' => $row['big_stage_id'],
                    'total' => 0,
                    'clear' => 0,
                    'tides' => [],
                    'clears' => [],
                ];
            }

            $results[$tmpId]['total'] += (int)$row['jobs'];
            $results[$tmpId]['clear'] += (int)$row['cleared'];
            $results[$tmpId]['tides'][$row['tide_id']] = (int)$row['jobs'];
            $results[$tmpId]['clears'][$row['tide_id']] = (int)$row['cleared'];
        }

        return \array_values($results);
    }

    private function getMaps(Connection $db): array
    {
        return ArrayHelper::map(
            SalmonMap3::find()->orderBy(['name' => SORT_ASC])->all(),
            'id',
            fn (SalmonMap3 $v): SalmonMap3 => $v,
        );
    }

    private function getTides(Connection $db): array
    {
        return ArrayHelper::map(
            SalmonWaterLevel2::find()->orderBy(['id' => SORT_ASC])->all($db),
            'id',
            fn (SalmonWaterLevel2 $model): SalmonWaterLevel2 => $model,
        );
    }

    private function getEvents(Connection $db): array
    {
        return ArrayHelper::map(
            SalmonEvent3::find()->orderBy(['id' => SORT_ASC])->all($db),
            'id',
            fn (SalmonEvent3 $model): SalmonEvent3 => $model,
        );
    }
}
