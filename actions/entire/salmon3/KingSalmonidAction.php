<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\salmon3;

use Yii;
use app\models\BigrunMap3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3MapKing;
use app\models\StatSalmon3MapKingTide;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_values;
use function sprintf;

use const SORT_ASC;

final class KingSalmonidAction extends Action
{
    public function run(): string
    {
        return $this->controller->render(
            'salmon3/king-salmonid',
            Yii::$app->db->transaction(
                fn (Connection $db): array => [
                    'bigMaps' => $this->getBigMaps($db),
                    'data' => $this->getData($db),
                    'dataWithTide' => $this->getDataWithTide($db),
                    'kings' => $this->getKings($db),
                    'maps' => $this->getMaps($db),
                    'tides' => $this->getTides($db),
                ],
                Transaction::READ_COMMITTED,
            ),
        );
    }

    private function getBigMaps(Connection $db): array
    {
        return ArrayHelper::asort(
            ArrayHelper::index(
                BigrunMap3::find()->all(),
                'id',
            ),
            fn (BigrunMap3 $a, BigrunMap3 $b): int => strnatcasecmp(
                Yii::t('app-map3', $a->name),
                Yii::t('app-map3', $b->name),
            ),
        );
    }

    private function getMaps(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonMap3::find()->orderBy(['id' => SORT_ASC])->all(),
            'id',
        );
    }

    private function getKings(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonKing3::find()
                ->orderBy(['id' => SORT_ASC])
                ->all($db),
            'id',
        );
    }

    private function getTides(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonWaterLevel2::find()
                ->orderBy(['id' => SORT_ASC])
                ->all($db),
            'id',
        );
    }

    private function getData(Connection $db): array
    {
        return StatSalmon3MapKing::find()
            ->orderBy([
                'map_id' => SORT_ASC,
                'big_map_id' => SORT_ASC,
                'king_id' => SORT_ASC,
            ])
            ->all($db);
    }

    private function getDataWithTide(Connection $db): array
    {
        return StatSalmon3MapKingTide::find()
            ->orderBy([
                'map_id' => SORT_ASC,
                'big_map_id' => SORT_ASC,
                'king_id' => SORT_ASC,
                'tide_id' => SORT_ASC,
            ])
            ->all($db);
    }
}
