<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\components\widgets\Alert;
use app\models\MaintenanceSchedule;
use yii\base\Widget;
use yii\helpers\Html;

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
        $fmt = Yii::$app->formatter;
        return Alert::widget([
            'options' => [
                'class' => 'alert-danger',
            ],
            'body' => implode('', [
                sprintf(
                    '<p><strong>%s</strong></p>',
                    Html::encode(Yii::t(
                        'app-alert',
                        'We\'ll perform maintenance on the schedule below:'
                    ))
                ),
                sprintf(
                    '<p>%s</p>',
                    Html::encode(Yii::t(
                        'app-alert',
                        'Term: {startDate} - {endDate}',
                        [
                            'startDate' => $fmt->asDateTime($model->start_at, 'short'),
                            'endDate' => $fmt->asDateTime($model->end_at, 'short'),
                        ]
                    ))
                ),
                sprintf(
                    '<p>%s</p>',
                    Html::encode(Yii::t(
                        'app-alert',
                        'Due to: {reason}',
                        [
                            'reason' => $model->reason,
                        ]
                    ))
                ),
                '<p>' . Html::encode(Yii::t('app-alert', 'Sorry for inconvenience.')) . '</p>',
            ]),
        ]);
    }
}
