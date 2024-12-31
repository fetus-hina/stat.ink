<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\TimezoneDialogAsset;
use app\components\widgets\Alert;
use app\models\MaintenanceSchedule;
use yii\base\Widget;
use yii\helpers\Html;

use function filter_var;
use function implode;
use function is_int;
use function sprintf;
use function vsprintf;

use const FILTER_VALIDATE_INT;

class MaintenanceInfo extends Widget
{
    public function run()
    {
        $model = MaintenanceSchedule::find()
            ->enabled()
            ->recently()
            ->limit(1)
            ->one();
        if (!$model) {
            return '';
        }

        return $this->renderWidget($model);
    }

    protected function renderWidget(MaintenanceSchedule $model): string
    {
        return Alert::widget([
            'options' => [
                'class' => 'alert-danger',
            ],
            'body' => implode('', [
                sprintf(
                    '<p><strong>%s</strong></p>',
                    Html::encode(Yii::t(
                        'app-alert',
                        'We\'ll perform maintenance on the schedule below:',
                    )),
                ),
                Html::tag(
                    'p',
                    Yii::t(
                        'app-alert',
                        'Term: {startDate} - {endDate}',
                        [
                            'startDate' => $this->formatTime($model->start_at),
                            'endDate' => $this->formatTime($model->end_at),
                        ],
                    ),
                ),
                Html::tag(
                    'p',
                    Html::encode(Yii::t(
                        'app-alert',
                        'Due to: {reason}',
                        [
                            'reason' => $model->reason,
                        ],
                    )),
                ),
                Html::tag('p', Html::encode(Yii::t('app-alert', 'Sorry for inconvenience.'))),
            ]),
        ]);
    }

    protected function formatTime($time): string
    {
        $timestamp = filter_var(
            Yii::$app->formatter->asTimestamp($time),
            FILTER_VALIDATE_INT,
        );
        if (!is_int($timestamp)) {
            return Html::encode(Yii::t('app', 'Unknown'));
        }

        $dt = (new DateTimeImmutable())
            ->setTimestamp($timestamp)
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));

        TimezoneDialogAsset::register($this->view);
        return Html::tag(
            'time',
            vsprintf('%s %s', [
                Html::encode(Yii::$app->formatter->asDateTime($dt, 'short')),
                Html::a(
                    Html::encode($dt->format('T')),
                    '#timezone-dialog',
                    [
                        'class' => 'alert-link',
                        'role' => 'button',
                        'aria-haspopup' => 'true',
                        'aria-expanded' => 'false',
                        'data' => [
                            'toggle' => 'modal',
                        ],
                    ],
                ),
            ]),
            [
                'datetime' => $dt->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->format(DateTime::ATOM),
            ],
        );
    }
}
