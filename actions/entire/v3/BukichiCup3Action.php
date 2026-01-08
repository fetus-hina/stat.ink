<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\TypeHelper;
use app\models\Event3;
use app\models\Event3StatsWeapon;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use const SORT_DESC;

final class BukichiCup3Action extends Action
{
    private const EVENT_ID = 'TGVhZ3VlTWF0Y2hFdmVudC1SYW5kb21XZWFwb24=';

    public function run(): Response
    {
        return Yii::$app->db->transaction(
            function (Connection $db): Response {
                $event = Event3::find()
                    ->andWhere(['{{%event3}}.[[internal_id]]' => self::EVENT_ID])
                    ->limit(1)
                    ->cache(86400)
                    ->one($db);
                if (!$event) {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }

                $eventSchedule = Event3StatsWeapon::find()
                    ->innerJoinWith(['schedule'], true)
                    ->andWhere(['{{%event_schedule3}}.[[event_id]]' => $event->id])
                    ->orderBy(['{{%event_schedule3}}.[[start_at]]' => SORT_DESC])
                    ->limit(1)
                    ->cache(3600)
                    ->one($db);
                if (!$eventSchedule) {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }

                $schedule = $eventSchedule->schedule;
                return TypeHelper::instanceOf($this->controller, Controller::class)
                    ->redirect(
                        ['entire/event3',
                            'event' => $schedule->event_id,
                            'schedule' => $schedule->id,
                        ],
                    );
            },
            Transaction::REPEATABLE_READ,
        );
    }
}
