<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

return [
  'label' => Yii::t('app', 'Note (private)'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (
      $model->private_note === null ||
      trim((string)$model->private_note) === ''
    ) {
      return null;
    }

    if (
      Yii::$app->user->isGuest ||
      (int)Yii::$app->user->id !== (int)$model->user_id
    ) {
      return null;
    }

    $view = Yii::$app->view;
    if ($view instanceof View) {
      $view->registerCss('#private-note{display:none}');
      $view->registerJs(
        '!function(a){"use strict";var o=a("#private-note-show"),e=a("#private-note"),n=a(".fa",o);' .
        'o.hover(function(){n.removeClass("fa-lock").addClass("fa-unlock-alt")},function(){' .
        'n.removeClass("fa-unlock-alt").addClass("fa-lock")}).click(function(){o.hide(),e.show()})}(jQuery);'
      );
    }

    return implode('', [
      Html::button(
        Html::tag('span', '', ['class' => 'fa fa-lock fa-fw']),
        ['class' => 'btn btn-default', 'id' => 'private-note-show']
      ),
      Html::tag(
        'div',
        Yii::$app->formatter->asNtext($model->private_note),
        ['id' => 'private-note']
      ),
    ]);
  },
];
