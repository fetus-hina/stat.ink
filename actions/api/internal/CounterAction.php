<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Exception;
use Throwable;
use Yii;
use app\models\Battle;
use app\models\Battle2;
use app\models\Salmon2;
use app\models\User;
use yii\db\Transaction;
use yii\web\HttpException;
use yii\web\ViewAction;

class CounterAction extends ViewAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        for ($retry = 0; $retry < 3; ++$retry) {
            try {
                return $this->make();
            } catch (Throwable $e) {
            }
        }

        throw new HttpException(503, 'Fetch failed');
    }

    private function make(): array
    {
        return Yii::$app->db->transaction(
            fn (): array => array_merge(
                $this->format('battle1', 'battle', 'Battles', Battle::getRoughCount()),
                $this->format('battle2', 'battle', 'Battles', Battle2::getRoughCount()),
                $this->format('salmon2', 'salmon', 'Shifts', Salmon2::getRoughCount()),
                $this->format('user', 'user', 'Users', User::getRoughCount()),
            ),
            Transaction::REPEATABLE_READ
        );
    }

    private function format(string $key, string $type, string $labelEn, ?int $count): array
    {
        if ($count === null || $count < 0) {
            throw new Exception();
        }

        return [
            $key => [
                'type' => $type,
                'label' => Yii::t('app-counter', $labelEn),
                'count' => (int)$count,
            ],
        ];
    }
}
