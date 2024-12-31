<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\show\v3\stats\SeasonXPowerAction;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Rule3;
use app\models\Season3;
use app\models\User;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @phpstan-import-type DailyData from SeasonXPowerAction
 *
 * @var Rule3[] $rule
 * @var Season3 $season,
 * @var User $user
 * @var View $this
 */

$am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);
echo Html::tag(
  'th',
  Html::a(
    Html::tag(
      'span',
      Html::encode(Yii::t('app-rule3', $rule->short_name)),
      ['class' => 'd-none d-md-inline'],
    ),
    ['show-v3/user',
      'screen_name' => $user->screen_name,
      'f' => [
        'lobby' => 'xmatch',
        'rule' => $rule->key,
        'term' => '@' . $season->key,
      ],
    ],
  ),
  [
    'class' => 'text-center',
    'colspan' => '3',
  ],
);
