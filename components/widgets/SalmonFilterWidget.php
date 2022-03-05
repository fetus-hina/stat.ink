<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Salmon2;
use app\models\SalmonFailReason2;
use app\models\SalmonMap2;
use app\models\SalmonSpecial2;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use const SORT_ASC;
use const SORT_STRING;

class SalmonFilterWidget extends Widget
{
    public $user;
    public $filter;

    public function run()
    {
        $this->view->registerCss(Html::renderCss([
            '#' . $this->id => [
                'border' => '1px solid #ccc',
                'border-radius' => '5px',
                'padding' => '15px',
                'margin-bottom' => '15px',
            ],
        ]));

        return Html::tag('div', $this->renderForm(), ['id' => $this->id]);
    }

    protected function renderForm(): string
    {
        ob_start();
        try {
            $form = ActiveForm::begin([
                'id' => $this->id . '-form',
                'action' => ['salmon/index', 'screen_name' => $this->user->screen_name],
                'method' => 'get',
            ]);
            echo $this->renderFields($form);
            echo Html::submitButton(
                implode(' ', [
                    FA::fas('search')->fw()->__toString(),
                    Html::encode(Yii::t('app', 'Search')),
                ]),
                [
                    'class' => 'btn btn-primary',
                ]
            );
            ActiveForm::end();
            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    protected function renderFields(ActiveForm $form): string
    {
        return implode('', [
            $this->renderStageField($form),
            $this->renderSpecialField($form),
            $this->renderResultField($form),
            $this->renderReasonField($form),
            $this->renderTermField($form),
            $this->renderFilterField($form),
        ]);
    }

    protected function renderStageField(ActiveForm $form): string
    {
        $stages = ArrayHelper::map(
            SalmonMap2::find()->asArray()->all(),
            'key',
            fn (array $row): string => Yii::t('app-salmon-map2', $row['name'])
        );
        asort($stages, SORT_STRING);

        return $form->field($this->filter, 'stage')
            ->dropDownList($stages, [
                'prompt' => Yii::t('app-map2', 'Any Stage'),
            ])
            ->label(false)
            ->render();
    }

    protected function renderSpecialField(ActiveForm $form): string
    {
        $specials = ArrayHelper::map(
            SalmonSpecial2::find()->asArray()->all(),
            'key',
            fn (array $row): string => Yii::t('app-special2', $row['name'])
        );
        asort($specials, SORT_STRING);

        return $form->field($this->filter, 'special')
            ->dropDownList($specials, [
                'prompt' => Yii::t('app-special2', 'Any Special'),
            ])
            ->label(false)
            ->render();
    }

    protected function renderResultField(ActiveForm $form): string
    {
        $list = [
            'cleared'   => Yii::t('app-salmon2', 'Cleared'),
            'failed' => Yii::t('app-salmon2', 'Failed'),
            'failed-wave3' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => 3,
            ]),
            'failed-wave2' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => 2,
            ]),
            'failed-wave1' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                'waveNumber' => 1,
            ]),
        ];

        return $form->field($this->filter, 'result')
            ->dropDownList($list, [
                'prompt' => vsprintf('%s / %s', [
                    Yii::t('app-salmon2', 'Cleared'),
                    Yii::t('app-salmon2', 'Failed'),
                ]),
            ])
            ->label(false)
            ->render();
    }

    protected function renderReasonField(ActiveForm $form): string
    {
        $reasons = ArrayHelper::map(
            SalmonFailReason2::find()->orderBy(['id' => SORT_ASC])->asArray()->all(),
            'key',
            fn (array $row): string => Yii::t('app-salmon2', $row['name'])
        );

        return $form->field($this->filter, 'reason')
            ->dropDownList($reasons, [
                'prompt' => Yii::t('app-salmon2', 'Fail Reason'),
            ])
            ->label(false)
            ->render();
    }

    protected function renderTermField(ActiveForm $form): string
    {
        $list = array_merge(
            [
                'this-rotation' => Yii::t('app-salmon2', 'This/Last Rotation'),
                'prev-rotation' => Yii::t('app-salmon2', 'Previous Rotation'),
            ],
            $this->filter->getValidVersions(),
            $this->getMonths(),
        );
        return $form->field($this->filter, 'term')
            ->dropDownList($list, [
                'prompt' => Yii::t('app', 'Any Time'),
            ])
            ->label(false)
            ->render();
    }

    protected function renderFilterField(ActiveForm $form): string
    {
        if (trim((string)$this->filter->filter) === '') {
            return '';
        }

        return $form->field($this->filter, 'filter')
            ->label(false)
            ->render();
    }

    private function getMonths(): array
    {
        $utc = new DateTimeZone('Etc/UTC');
        $now = (new DateTimeImmutable())
            ->setTimezone($utc)
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()));
        $firstCreatedAt = Salmon2::find()->min('created_at') ?: $now->format(DateTime::ATOM);
        $minDate = (new DateTimeImmutable($firstCreatedAt, $utc));
        $minDate = $minDate
            ->setTime(0, 0, 0)
            ->setDate((int)$minDate->format('Y'), (int)$minDate->format('n'), 1);
        $maxDate = (new DateTimeImmutable())
            ->setTimezone($utc)
            ->setTime(0, 0, 0)
            ->setDate((int)$now->format('Y'), (int)$now->format('n'), 1);
        $results = [];
        for ($t = $maxDate; $t >= $minDate; $t = $t->sub(new DateInterval('P1M'))) {
            $results[$t->format('Y-m')] = Yii::$app->formatter->asDate($t, Yii::t('app', 'MMMM y'));
        }
        return $results;
    }
}
