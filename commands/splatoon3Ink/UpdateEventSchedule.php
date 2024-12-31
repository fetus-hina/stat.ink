<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\splatoon3Ink;

use DateTimeInterface;
use Exception;
use UnexpectedValueException;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Event3;
use app\models\EventMap3;
use app\models\EventPeriod3;
use app\models\EventSchedule3;
use app\models\Rule3;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_map;
use function count;
use function fwrite;
use function gmdate;
use function is_array;
use function is_string;
use function max;
use function min;
use function strtotime;
use function vfprintf;

use const STDERR;

trait UpdateEventSchedule
{
    protected function updateEventSchedule(array $schedules): int
    {
        $events = ArrayHelper::getValue($schedules, 'event');
        if (!is_array($events)) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return Yii::$app->db->transaction(
            function (Connection $db) use ($events): int {
                foreach ($events as $event) {
                    if (!$this->registerEventSchedule($event)) {
                        $db->transaction->rollBack();
                        return ExitCode::UNSPECIFIED_ERROR;
                    }
                }

                return ExitCode::OK;
            },
            Transaction::REPEATABLE_READ,
        );
    }

    private function registerEventSchedule(array $eventData): bool
    {
        $event = $this->registerEventBaseData($eventData);
        $schedule = $this->registerEventSchedule3($event, $eventData);
        $this->registerEventMaps($schedule, $eventData);
        $this->registerEventPeriods($schedule, $eventData);

        return true;
    }

    private function registerEventBaseData(array $eventData): Event3
    {
        $internalId = ArrayHelper::getValue($eventData, 'id');
        if (!is_string($internalId)) {
            throw new UnexpectedValueException('Event id does not exist');
        }

        $model = Event3::find()
            ->andWhere(['internal_id' => $internalId])
            ->limit(1)
            ->one();
        if (!$model) {
            $model = Yii::createObject([
                'class' => Event3::class,
                'internal_id' => $internalId,
            ]);
        }

        $name = TypeHelper::string(ArrayHelper::getValue($eventData, 'name'));
        $desc = TypeHelper::stringOrNull(ArrayHelper::getValue($eventData, 'desc'));
        $regu = TypeHelper::stringOrNull(ArrayHelper::getValue($eventData, 'regulation'));

        if (
            $name !== $model->name ||
            $desc !== $model->desc ||
            $regu !== $model->regulation
        ) {
            $model->name = $name;
            $model->desc = $desc;
            $model->regulation = $regu;
            if (!$model->save()) {
                throw new Exception('Failed to save Event3');
            }

            fwrite(STDERR, "Event3 created/updated: $name\n");
        }

        return $model;
    }

    private function registerEventSchedule3(Event3 $event, array $data): EventSchedule3
    {
        $startAt = min(
            array_map(
                fn (array $period): int => $period['start_at'],
                $data['periods'],
            ),
        );
        $endAt = max(
            array_map(
                fn (array $period): int => $period['end_at'],
                $data['periods'],
            ),
        );
        if (!$startAt || !$endAt) {
            throw new Exception('Invalid periods');
        }

        $rule = Rule3::find()
            ->andWhere(['id' => ArrayHelper::getValue($data, 'rule_id')])
            ->limit(1)
            ->one();
        if (!$rule) {
            throw new Exception('Invalid rule');
        }

        $model = EventSchedule3::find()
            ->andWhere(['start_at' => gmdate(DateTimeInterface::ATOM, $startAt)])
            ->limit(1)
            ->one();
        if (!$model) {
            $model = Yii::createObject([
                'class' => EventSchedule3::class,
            ]);
        }

        if (
            $model->event_id !== $event->id ||
            $model->rule_id !== $rule->id ||
            $model->start_at === null ||
            $model->end_at === null ||
            strtotime($model->start_at) !== $startAt ||
            strtotime($model->end_at) !== $endAt
        ) {
            $model->event_id = $event->id;
            $model->rule_id = $rule->id;
            $model->start_at = gmdate(DateTimeInterface::ATOM, $startAt);
            $model->end_at = gmdate(DateTimeInterface::ATOM, $endAt);
            if (!$model->save()) {
                throw new Exception('Failed to create/udate EventSchedule3');
            }

            fwrite(STDERR, "EventSchedule3 created/updated\n");
        }

        return $model;
    }

    private function registerEventMaps(EventSchedule3 $schedule, array $data): void
    {
        $mapIds = ArrayHelper::getValue($data, 'map_ids');
        if (!is_array($mapIds) || !$mapIds) {
            throw new Exception('Invalid stages');
        }

        // 一致しないデータを消す
        EventMap3::deleteAll(['and',
            ['schedule_id' => $schedule->id],
            ['not', ['map_id' => $mapIds]],
        ]);

        foreach ($mapIds as $mapId) {
            $model = EventMap3::find()
                ->andWhere([
                    'schedule_id' => $schedule->id,
                    'map_id' => $mapId,
                ])
                ->limit(1)
                ->one();
            if (!$model) {
                $model = Yii::createObject([
                    'class' => EventMap3::class,
                    'schedule_id' => $schedule->id,
                    'map_id' => $mapId,
                ]);
                if (!$model->save()) {
                    throw new Exception("Failed to register EventMap3 ({$schedule->id} / {$mapId})");
                }

                fwrite(STDERR, "Registered EventMap3 ({$schedule->id} / {$mapId})\n");
            }
        }
    }

    private function registerEventPeriods(EventSchedule3 $schedule, array $data): void
    {
        $convData = array_map(
            fn (array $period): array => [
                'start_at' => gmdate(DateTimeInterface::ATOM, $period['start_at']),
                'end_at' => gmdate(DateTimeInterface::ATOM, $period['end_at']),
            ],
            ArrayHelper::getValue($data, 'periods'),
        );

        $timeFilter = ['or'];
        foreach ($convData as $tmp) {
            $timeFilter[] = ['and',
                [
                    'start_at' => $tmp['start_at'],
                    'end_at' => $tmp['end_at'],
                ],
            ];
        }

        $exists = EventPeriod3::find()
            ->andWhere(['and',
                ['schedule_id' => $schedule->id],
                $timeFilter,
            ])
            ->count();
        if ($exists === count($convData)) {
            // OK
            return;
        }

        // 何かが違うので、全部消して作り直す
        EventPeriod3::deleteAll(['schedule_id' => $schedule->id]);
        foreach ($convData as $tmp) {
            $model = Yii::createObject([
                'class' => EventPeriod3::class,
                'schedule_id' => $schedule->id,
                'start_at' => $tmp['start_at'],
                'end_at' => $tmp['end_at'],
            ]);
            if (!$model->save()) {
                throw new Exception('Failed to create EventPeriod');
            }

            vfprintf(STDERR, "Registered EventPeriod: schedule=%s, %s-%s\n", [
                $model->schedule_id,
                $model->start_at,
                $model->end_at,
            ]);
        }
    }
}
