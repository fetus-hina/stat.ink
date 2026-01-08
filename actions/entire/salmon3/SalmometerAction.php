<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\salmon3;

use Yii;
use app\models\StatSalmon3Salmometer;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

final class SalmometerAction extends Action
{
    public function run(): string
    {
        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => [
                'data' => $this->getData($db),
            ],
            Transaction::REPEATABLE_READ,
        );

        return $this->controller->render('salmon3/salmometer', $data);
    }

    /**
     * @return array<int, StatSalmon3Salmometer>
     */
    private function getData(Connection $db): array
    {
        return ArrayHelper::index(
            StatSalmon3Salmometer::find()
                ->orderBy(['king_smell' => SORT_ASC])
                ->all($db),
            'king_smell',
        );
    }
}
