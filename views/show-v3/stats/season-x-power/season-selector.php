<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Season3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Season3[] $seasons,
 * @var Season3 $season,
 * @var User $user
 * @var View $this
 */

echo Html::tag(
  'div',
  Html::dropDownList(
    name: '',
    selection: Url::to(
      ['show-v3/stats-season-x-power',
        'screen_name' => $user->screen_name,
        'season' => $season->id,
      ],
      true,
    ),
    items: ArrayHelper::map(
      $seasons,
      fn (Season3 $current): string => Url::to(
        ['show-v3/stats-season-x-power',
          'screen_name' => $user->screen_name,
          'season' => $current->id,
        ],
        true,
      ),
      fn (Season3 $current): string => Yii::t('app-season3', $current->name),
    ),
    options: [
      'class' => ['form-control', 'm-0'],
      'onchange' => 'window.location.href=this.value;',
    ],
  ),
  ['class' => 'mb-3'],
);
