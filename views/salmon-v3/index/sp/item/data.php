<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

echo Html::tag(
  'div',
  implode('', [
    $this->render('stage', ['model' => $model]),
    $this->render('hazard-level', ['model' => $model]),
    $this->render('title', ['model' => $model]),
    $this->render('eggs', ['model' => $model]),
  ]),
  ['class' => 'simple-battle-data'],
);
