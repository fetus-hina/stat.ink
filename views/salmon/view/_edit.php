<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon2 $model
 * @var View $this
 */

if (!$model->isEditable) {
  return;
}

?>
<div class="text-right" style="margin-bottom:15px">
  <?= Html::a(
    Html::encode(Yii::t('app', 'Edit')),
    ['salmon/edit',
      'screen_name' => $model->user->screen_name,
      'id' => $model->id,
    ],
    [
      'class' => 'btn btn-default',
      'rel' => 'nofollow',
    ]
  ) . "\n" ?>
</div>
