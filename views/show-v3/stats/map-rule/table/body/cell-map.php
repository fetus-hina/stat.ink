<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3 $map
 * @var User $user
 * @var View $this
 */

echo Html::tag(
  'th',
  Html::a(
    implode('', [
      Html::tag(
        'div',
        Html::tag(
          'span',
          Html::encode(Yii::t('app-map3', $map->short_name)),
          [
            'class' => 'auto-tooltip',
            'title' => Yii::t('app-map3', $map->name),
          ],
        ),
        ['class' => 'omit'],
      ),
    ]),
    ['show-v3/user',
      'screen_name' => $user->screen_name,
      'f' => [
        'map' => $map->key,
        'result' => '~win_lose',
      ],
    ],
  ),
  [
    'class' => 'text-center align-middle',
    'scope' => 'row',
  ],
);
