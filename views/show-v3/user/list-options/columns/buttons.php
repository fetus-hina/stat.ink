<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\EmbedVideo;
use app\components\widgets\Icon;
use app\models\Battle3;
use yii\helpers\Html;

return [
  'format' => 'raw',
  'value' => fn (Battle3 $model): string => trim(
    implode(' ', [
      Html::a(
        Html::encode(Yii::t('app', 'Detail')),
        ['show-v3/battle',
          'screen_name' => $model->user->screen_name,
          'battle' => $model->uuid,
        ],
        ['class' => 'btn btn-primary btn-xs']
      ),
      (!$model->link_url)
        ? ''
        : Html::a(
          EmbedVideo::isSupported($model->link_url)
            ? Icon::videoLink()
            : Icon::link(),
          $model->link_url,
          [
            'class' => 'btn btn-default btn-xs',
            'rel' => 'nofollow',
          ],
        ),
    ]),
  ),
  'contentOptions' => [
    'class' => 'nobr',
  ],
];
