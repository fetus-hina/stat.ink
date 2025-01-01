<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\KDWin3FilterForm;
use app\models\Lobby3;
use app\models\Season3;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var KDWin3FilterForm $filter
 * @var View $this
 * @var array<string, Lobby3> $lobbies
 * @var array<string, Season3> $seasons
 */

$this->registerCss('#filter-form .help-block{display:none}');

$form = ActiveForm::begin([
  'action' => ['entire/kd-win3'],
  'enableClientValidation' => false,
  'id' => 'filter-form',
  'method' => 'get',
  'options' => ['class' => 'form-inline mb-3'],
]);

echo Html::tag(
  'span',
  $form->field($filter, 'lobby')
    ->label(false)
    ->dropDownList(
      array_map(
        fn (Lobby3 $model): string => Yii::t('app-lobby3', $model->name),
        $lobbies,
      ),
      ['prompt' => Yii::t('app-lobby3', 'Any Lobby')],
    ),
  ['class' => 'mr-2'],
);

echo Html::tag(
  'span',
  $form->field($filter, 'season')
    ->label(false)
    ->dropDownList(
      array_map(
        fn (Season3 $model): string => Yii::t('app-season3', $model->name),
        $seasons,
      ),
    ),
  ['class' => 'mr-2'],
);

echo Html::tag(
  'div',
  Html::submitButton(
    implode(' ', [
      Icon::filter(),
      Html::encode(Yii::t('app', 'Summarize')),
    ]),
    ['class' => 'btn btn-primary'],
  ),
  ['class' => 'form-group'],
);

ActiveForm::end();
