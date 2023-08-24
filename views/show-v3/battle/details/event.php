<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Battle3;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

return [
  'label' => Yii::t('app-lobby3', 'Challenge'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if (!$event = $model->event) {
      return null;
    }

    if ($event->regulation) {
      TypeHelper::instanceOf(Yii::$app->view, View::class)->registerJs(
        vsprintf('$(%s).popover(%s);', [
          Json::encode('#event-regulation'),
          Json::encode([
            'content' => Html::tag('small', Yii::t('db/event3/regulation', $event->regulation)),
            'html' => true,
            'placement' => 'bottom',
            'title' => Html::encode(Yii::t('db/event3', $event->name)),
            'trigger' => 'hover',
          ]),
        ]),
      );
    }

    return Html::tag(
      'div',
      implode('<br>', array_filter(
        [
          Html::encode(Yii::t('db/event3', $event->name)),
          $event->desc
            ? Html::tag(
              'small',
              Html::encode(Yii::t('db/event3/description', $event->desc)),
              ['class' => 'text-muted'],
            )
            : null,
        ],
        fn (?string $s): bool => $s !== null,
      )),
      [
        'class' => 'd-inline-block',
        'id' => 'event-regulation',
      ],
    );
  },
];
