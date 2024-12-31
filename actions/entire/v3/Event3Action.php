<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\Event3;
use app\models\Event3StatsPower;
use app\models\Event3StatsPowerHistogram;
use app\models\Event3StatsPowerPeriod;
use app\models\Event3StatsPowerPeriodHistogram;
use app\models\Event3StatsSpecial;
use app\models\Event3StatsWeapon;
use app\models\EventSchedule3;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function array_filter;
use function array_values;
use function filter_var;
use function is_int;
use function strnatcasecmp;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;
use const SORT_DESC;

final class Event3Action extends Action
{
    public function run(): string|Response
    {
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);

        $dataOrResponse = Yii::$app->db->transaction(
            function (Connection $db) use ($controller): array|Response {
                [$needRedirect, $event] = self::getEvent($db);
                if ($needRedirect) {
                    return $controller->redirect(
                        ['entire/event3',
                            'event' => $event->id,
                            'schedule' => self::getSchedule($db, $event)[1]->id,
                        ],
                    );
                }

                [$needRedirect, $schedule] = self::getSchedule($db, $event);
                if ($needRedirect) {
                    return $controller->redirect(
                        ['entire/event3',
                            'event' => $event->id,
                            'schedule' => $schedule->id,
                        ],
                    );
                }

                return [
                    'abstract' => self::getAbstract($db, $schedule),
                    'event' => $event,
                    'events' => self::getEvents($db),
                    'histogram' => self::getHistogram($db, $schedule),
                    'periodAbstracts' => self::getPeriodAbstracts($db, $schedule),
                    'periodHistogram' => self::getPeriodHistogram($db, $schedule),
                    'schedule' => $schedule,
                    'schedules' => self::getSchedules($db, $event),
                    'specialProvider' => self::getSpecialProvider($db, $schedule),
                    'weaponsProvider' => self::getWeaponsProvider($db, $schedule),
                ];
            },
            Transaction::REPEATABLE_READ,
        );

        return $dataOrResponse instanceof Response
            ? $dataOrResponse
            : $controller->render('v3/event3', $dataOrResponse);
    }

    /**
     * @return array{bool, Event3} bool: need redirect?
     */
    private static function getEvent(Connection $db): array
    {
        $request = TypeHelper::instanceOf(Yii::$app->request, Request::class);
        $id = $request->get('event');
        if ($id === null || $id === '') {
            $model = Event3StatsWeapon::find()
                ->innerJoinWith(['schedule'], true)
                ->with(['schedule.event'])
                ->orderBy(['{{%event_schedule3}}.[[start_at]]' => SORT_DESC])
                ->limit(1)
                ->one($db);
            $event = $model?->schedule?->event;
            return $event
                ? [true, $event]
                : throw new ServerErrorHttpException('No schedule found');
        }

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            $event = Event3::find()
                ->andWhere(['id' => $id])
                ->limit(1)
                ->one($db);

            if ($event) {
                return [false, $event];
            }
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * @return array{bool, EventSchedule3} bool: need redirect?
     */
    private static function getSchedule(Connection $db, Event3 $event): array
    {
        $request = TypeHelper::instanceOf(Yii::$app->request, Request::class);
        $id = $request->get('schedule');
        if ($id === null || $id === '') {
            $model = Event3StatsWeapon::find()
                ->innerJoinWith(['schedule'], true)
                ->andWhere(['{{%event_schedule3}}.[[event_id]]' => $event->id])
                ->orderBy(['{{%event_schedule3}}.[[start_at]]' => SORT_DESC])
                ->limit(1)
                ->one($db);

            if (!$model) {
                throw new ServerErrorHttpException('No schedule found');
            }

            return [true, $model->schedule];
        }

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (is_int($id)) {
            $model = EventSchedule3::find()
                ->andWhere([
                    'event_id' => $event->id,
                    'id' => $id,
                ])
                ->limit(1)
                ->one($db);

            if ($model) {
                return [false, $model];
            }
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * @return Event3[]
     */
    private static function getEvents(Connection $db): array
    {
        $eventIds = (new Query())
            ->select(['event_id' => '{{%event_schedule3}}.[[event_id]]'])
            ->distinct()
            ->from('{{%event_schedule3}}')
            ->innerJoin(
                '{{%event3_stats_power}}',
                '{{%event3_stats_power}}.[[schedule_id]] = {{%event_schedule3}}.[[id]]',
            )
            ->cache(60)
            ->column($db);

        return ArrayHelper::sort(
            Event3::find()
                ->andWhere(['id' => $eventIds])
                ->cache(86400)
                ->all($db),
            fn (Event3 $a, Event3 $b): int => strnatcasecmp(
                Yii::t('db/event3', $a->name),
                Yii::t('db/event3', $b->name),
            ),
        );
    }

    /**
     * @return EventSchedule3[]
     */
    private static function getSchedules(Connection $db, Event3 $event): array
    {
        $list = EventSchedule3::find()
            ->with(['rule'])
            ->andWhere(['event_id' => $event->id])
            ->orderBy(['start_at' => SORT_DESC])
            ->cache(86400)
            ->all($db);
        return array_values(
            array_filter(
                $list,
                fn (EventSchedule3 $model): bool => Event3StatsWeapon::find()
                    ->andWhere([
                        'schedule_id' => $model->id,
                    ])
                    ->cache(300)
                    ->exists(),
            ),
        );
    }

    private static function getSpecialProvider(
        Connection $db,
        EventSchedule3 $schedule,
    ): ActiveDataProvider {
        $query = Event3StatsSpecial::find()
            ->with([
                'special',
            ])
            ->andWhere(['schedule_id' => $schedule->id])
            ->orderBy([
                'battles' => SORT_DESC,
                'wins' => SORT_DESC,
                'special_id' => SORT_DESC,
            ]);

        return new ActiveDataProvider([
            'key' => 'special_id',
            'pagination' => false,
            'query' => $query,
            'sort' => false,
        ]);
    }

    private static function getWeaponsProvider(
        Connection $db,
        EventSchedule3 $schedule,
    ): ActiveDataProvider {
        $query = Event3StatsWeapon::find()
            ->with([
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->andWhere(['schedule_id' => $schedule->id])
            ->orderBy([
                'battles' => SORT_DESC,
                'wins' => SORT_DESC,
                'weapon_id' => SORT_DESC,
            ]);

        return new ActiveDataProvider([
            'key' => 'weapon_id',
            'pagination' => false,
            'query' => $query,
            'sort' => false,
        ]);
    }

    private static function getAbstract(Connection $db, EventSchedule3 $schedule): ?Event3StatsPower
    {
        return Event3StatsPower::find()
            ->andWhere(['schedule_id' => $schedule->id])
            ->limit(1)
            ->one($db);
    }

    /**
     * @return Event3StatsPowerHistogram[]
     */
    private static function getHistogram(Connection $db, EventSchedule3 $schedule): array
    {
        return Event3StatsPowerHistogram::find()
            ->andWhere(['schedule_id' => $schedule->id])
            ->orderBy(['class_value' => SORT_ASC])
            ->all($db);
    }

    /**
     * @return array<int, Event3StatsPowerPeriod>
     */
    private static function getPeriodAbstracts(Connection $db, EventSchedule3 $schedule): array
    {
        return ArrayHelper::index(
            Event3StatsPowerPeriod::find()
                ->innerJoinWith(['period'], false)
                ->andWhere([
                    '{{%event_period3}}.[[schedule_id]]' => $schedule->id,
                ])
                ->orderBy(['period_id' => SORT_ASC])
                ->all($db),
            'period_id',
        );
    }

    private static function getPeriodHistogram(Connection $db, EventSchedule3 $schedule): array
    {
        return Event3StatsPowerPeriodHistogram::find()
            ->innerJoinWith(['period'], false)
            ->andWhere(['{{%event_period3}}.[[schedule_id]]' => $schedule->id])
            ->orderBy([
                '{{%event3_stats_power_period_histogram}}.[[period_id]]' => SORT_ASC,
                '{{%event3_stats_power_period_histogram}}.[[class_value]]' => SORT_ASC,
            ])
            ->all($db);
    }
}
