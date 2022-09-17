<?php

declare(strict_types=1);

use app\components\widgets\EmbedVideo;
use app\models\Battle3;
use yii\web\View;

/**
 * @var Battle3 $model
 * @var View $this
 */

if ($model->link_url && EmbedVideo::isSupported($model->link_url)) {
  $this->registerCss('.video{margin-bottom:15px}');
  echo EmbedVideo::widget(['url' => $model->link_url]);
}
