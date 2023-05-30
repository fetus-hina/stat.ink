<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\splatoon3Ink;

use DateTime;
use LogicException;
use Yii;
use app\models\SalmonKing3;
use app\models\SalmonRandom3;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\SalmonWeapon3;
use yii\console\ExitCode;
use yii\db\Connection;

use function array_values;
use function count;
use function date;
use function vfprintf;

use const SORT_ASC;
use const STDERR;

trait UpdateSalmonSchedule
{
    protected function updateSalmonSchedule(array $schedules): int
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $hasError = false;
        foreach ($schedules['salmon_regular'] as $info) {
            if (
                !$this->registerSalmonSchedule(
                    startAt: $info['startAt'],
                    endAt: $info['endAt'],
                    mapId: $info['map_id'],
                    king: $info['king'],
                    weapons: $info['weapons'],
                    isBigRun: false,
                    isEggstraWork: false,
                )
            ) {
                $hasError = true;
            }
        }

        foreach ($schedules['salmon_bigrun'] as $info) {
            if (
                !$this->registerSalmonSchedule(
                    startAt: $info['startAt'],
                    endAt: $info['endAt'],
                    mapId: $info['map_id'],
                    king: $info['king'],
                    weapons: $info['weapons'],
                    isBigRun: true,
                    isEggstraWork: false,
                )
            ) {
                $hasError = true;
            }
        }

        foreach ($schedules['salmon_eggstra'] as $info) {
            if (
                !$this->registerSalmonSchedule(
                    startAt: $info['startAt'],
                    endAt: $info['endAt'],
                    mapId: $info['map_id'],
                    king: null, // $info['king'],
                    weapons: $info['weapons'],
                    isBigRun: false,
                    isEggstraWork: true,
                )
            ) {
                $hasError = true;
            }
        }

        return $hasError ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    /**
     * @param array<SalmonWeapon3|SalmonRandom3|null> $weapons
     */
    private function registerSalmonSchedule(
        int $startAt,
        int $endAt,
        ?int $mapId,
        ?SalmonKing3 $king,
        array $weapons,
        bool $isBigRun,
        bool $isEggstraWork,
    ): bool {
        return Yii::$app->db->transaction(
            function (Connection $db) use ($startAt, $endAt, $mapId, $king, $weapons, $isBigRun, $isEggstraWork): bool {
                // 既にデータが正しく保存されている
                if (
                    $this->isSalmonScheduleRegistered(
                        $startAt,
                        $endAt,
                        $mapId,
                        $king,
                        $weapons,
                        $isBigRun,
                        $isEggstraWork,
                    )
                ) {
                    return true;
                }

                if (
                    $this->cleanUpSalmonSchedule($startAt, $isEggstraWork) &&
                    $this->registerSalmonScheduleImpl(
                        $startAt,
                        $endAt,
                        $mapId,
                        $king,
                        $weapons,
                        $isBigRun,
                        $isEggstraWork,
                    )
                ) {
                    vfprintf(STDERR, "Salmon run schedule registered, %s, %s, period=%s - %s\n", [
                        $isEggstraWork ? 'eggstra' : 'standard',
                        $isBigRun ? 'bigrun' : 'standard',
                        date(DateTime::ATOM, $startAt),
                        date(DateTime::ATOM, $endAt),
                    ]);

                    return true;
                }

                $db->transaction->rollBack();
                return false;
            },
        );
    }

    /**
     * @param array<SalmonWeapon3|SalmonRandom3|null> $weapons
     */
    private function isSalmonScheduleRegistered(
        int $startAt,
        int $endAt,
        ?int $mapId,
        ?SalmonKing3 $king,
        array $weapons,
        bool $isBigRun,
        bool $isEggstraWork,
    ): bool {
        $schedule = SalmonSchedule3::find()
            ->andWhere([
                'end_at' => date(DateTime::ATOM, $endAt),
                'is_eggstra_work' => $isEggstraWork,
                'king_id' => $king?->id ?? null,
                'start_at' => date(DateTime::ATOM, $startAt),
            ])
            ->andWhere(
                $isBigRun
                    ? [
                        'big_map_id' => $mapId,
                        'map_id' => null,
                    ]
                    : [
                        'big_map_id' => null,
                        'map_id' => $mapId,
                    ],
            )
            ->limit(1)
            ->one();
        if (!$schedule) {
            // データないか何かおかしい
            return false;
        }

        $dbWeapons = SalmonScheduleWeapon3::find()
            ->andWhere(['schedule_id' => $schedule->id])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        if (count($dbWeapons) !== count($weapons)) {
            // 個数が一致しないはずがない
            return false;
        }

        foreach (array_values($weapons) as $i => $weapon) {
            if ($weapon instanceof SalmonWeapon3) {
                if ($dbWeapons[$i]->weapon_id !== $weapon->id) {
                    return false;
                }
            } elseif ($weapon instanceof SalmonRandom3) {
                if ($dbWeapons[$i]->random_id !== $weapon->id) {
                    return false;
                }
            } else {
                throw new LogicException();
            }
        }

        return true;
    }

    private function cleanUpSalmonSchedule(int $startAt, bool $isEggstraWork): bool
    {
        $model = SalmonSchedule3::find()
            ->andWhere([
                'start_at' => date(DateTime::ATOM, $startAt),
                'is_eggstra_work' => $isEggstraWork,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            return true;
        }

        SalmonScheduleWeapon3::deleteAll(['schedule_id' => $model->id]);
        $model->delete();

        return true;
    }

    /**
     * @param array<SalmonWeapon3|SalmonRandom3|null> $weapons
     */
    private function registerSalmonScheduleImpl(
        int $startAt,
        int $endAt,
        ?int $mapId,
        ?SalmonKing3 $king,
        array $weapons,
        bool $isBigRun,
        bool $isEggstraWork,
    ): bool {
        $schedule = Yii::createObject([
            'class' => SalmonSchedule3::class,
            'big_map_id' => $isBigRun ? $mapId : null,
            'end_at' => date(DateTime::ATOM, $endAt),
            'is_eggstra_work' => $isEggstraWork,
            'king_id' => $king?->id ?? null,
            'map_id' => $isBigRun ? null : $mapId,
            'start_at' => date(DateTime::ATOM, $startAt),
        ]);
        if (!$schedule->save()) {
            return false;
        }

        foreach ($weapons as $weapon) {
            $model = Yii::createObject([
                'class' => SalmonScheduleWeapon3::class,
                'schedule_id' => $schedule->id,
                'weapon_id' => null,
                'random_id' => null,
            ]);
            if ($weapon instanceof SalmonWeapon3) {
                $model->weapon_id = $weapon->id;
            } elseif ($weapon instanceof SalmonRandom3) {
                $model->random_id = $weapon->id;
            } elseif ($weapon === null) {
                continue;
            } else {
                throw new LogicException();
            }

            if (!$model->save()) {
                return false;
            }
        }

        return true;
    }
}
