<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\splatoon3Ink;

use DateTimeInterface;
use Exception;
use Yii;
use app\models\Splatfest3;
use app\models\SplatfestTeam3;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Transaction;

use function gmdate;
use function strtotime;

trait UpdateSplatfestSchedule
{
    protected function updateSplatfestSchedule(array $festivals): int
    {
        return Yii::$app->db->transaction(
            function (Connection $db) use ($festivals): int {
                foreach ($festivals as $key => $node) {
                    if (!$this->registerSplatfestSchedule($key, $node)) {
                        $db->transaction->rollBack();
                        return ExitCode::UNSPECIFIED_ERROR;
                    }
                }

                return ExitCode::OK;
            },
            Transaction::REPEATABLE_READ,
        );
    }

    private function registerSplatfestSchedule(string $key, array $festData): bool
    {
        $model = $this->registerSplatfestBaseData($key, $festData);
        foreach ($festData['teams'] as $teamKey => $teamData) {
            if (!$this->registerSplatfestTeam($model, $teamKey, $teamData)) {
                return false;
            }
        }

        return true;
    }

    private function registerSplatfestBaseData(string $key, array $festData): Splatfest3
    {
        $model = Splatfest3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one();
        if (!$model) {
            $model = Yii::createObject([
                'class' => Splatfest3::class,
                'key' => $key,
            ]);
        }

        if ($model->name !== $festData['title']) {
            $model->name = $festData['title'];
        }

        if (!$model->start_at || @strtotime($model->start_at) !== $festData['startAt']) {
            $model->start_at = gmdate(DateTimeInterface::ATOM, $festData['startAt']);
        }

        if (!$model->end_at || @strtotime($model->end_at) !== $festData['endAt']) {
            $model->end_at = gmdate(DateTimeInterface::ATOM, $festData['endAt']);
        }

        if ($model->dirtyAttributes) {
            if (!$model->save()) {
                throw new Exception('Failed to save Splatfest3');
            }
        }

        return $model;
    }

    private function registerSplatfestTeam(Splatfest3 $fest, string $teamKey, array $teamData): bool
    {
        $campId = match ($teamKey) {
            'alpha' => 1,
            'bravo' => 2,
            'charlie' => 3,
            default => throw new Exception('Unknown team key: ' . $teamKey),
        };

        $model = SplatfestTeam3::find()
            ->andWhere([
                'fest_id' => $fest->id,
                'camp_id' => $campId,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            $model = Yii::createObject([
                'class' => SplatfestTeam3::class,
                'fest_id' => $fest->id,
                'camp_id' => $campId,
            ]);
        }

        if ($model->name !== $teamData['name']) {
            $model->name = $teamData['name'];
        }

        if ($model->color !== $teamData['color']) {
            $model->color = $teamData['color'];
        }

        if ($model->dirtyAttributes) {
            if (!$model->save()) {
                throw new Exception('Failed to save SplatfestTeam3');
            }
        }

        return true;
    }
}
