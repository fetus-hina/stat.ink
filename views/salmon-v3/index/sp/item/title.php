<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Label;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

echo Html::tag(
  'div',
  $model->titleAfter
    ? Html::encode(
      trim(
        vsprintf('%s %s', [
          Yii::t('app-salmon-title3', $model->titleAfter->name),
          Yii::$app->formatter->asInteger($model->title_exp_after),
        ]),
      ),
    )
    : Html::encode(mb_chr(0xa0, 'UTF-8')),
  ['class' => 'omit simple-battle-weapon'],
);
