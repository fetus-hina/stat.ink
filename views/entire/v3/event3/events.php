<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Event3;
use app\models\EventSchedule3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Event3 $event
 * @var Event3[] $events
 * @var View $this
 */

$placeholder = '--id--';

?>
<div class="mb-1">
  <?= Html::dropDownList(
    'event',
    (string)$event->id,
    ArrayHelper::map(
      $events,
      fn (Event3 $event): string => (string)$event->id,
      fn (Event3 $event): string => Yii::t('db/event3', $event->name),
    ),
    [
      'class' => 'form-control mb-0',
      'data' => [
        'template' => Url::to(['entire/event3', 'event' => $placeholder], true),
      ],
      'onchange' => vsprintf('window.location.href = this.dataset.template.replace(%s, this.value)', [
        Json::encode($placeholder),
      ]),
    ],
  ) . "\n" ?>
</div>
