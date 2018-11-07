<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\models\SalmonMap2;
use app\models\SalmonSpecial2;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

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
        return ob_get_clean();
    }

    protected function renderFields(ActiveForm $form): string
    {
        return implode('', [
            $this->renderStageField($form),
            $this->renderSpecialField($form),
        ]);
    }

    protected function renderStageField(ActiveForm $form): string
    {
        $stages = [];
        foreach (SalmonMap2::find()->asArray()->all() as $row) {
            $stages[$row['key']] = Yii::t('app-salmon-map2', $row['name']);
        }
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
        $stages = [];
        foreach (SalmonSpecial2::find()->asArray()->all() as $row) {
            $stages[$row['key']] = Yii::t('app-special2', $row['name']);
        }
        asort($stages, SORT_STRING);

        return $form->field($this->filter, 'special')
            ->dropDownList($stages, [
                'prompt' => Yii::t('app-special2', 'Any Special'),
            ])
            ->label(false)
            ->render();
    }
}
