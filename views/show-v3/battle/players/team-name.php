<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Splatfest3Theme;
use app\models\TricolorRole3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Splatfest3Theme|null $theme
 * @var TricolorRole3|null $role
 * @var View $this
 * @var bool $ourTeam
 */

if ($theme || $role) {
  echo implode(
    ' ',
    array_filter(
      [
        match ($role?->key) {
          'attacker' => Icon::s3TricolorAttacker(),
          'defender' => Icon::s3TricolorDefender(),
          default => null,
        },
        $theme
          ? Html::encode($theme->name)
          : null,
        $role
          ? vsprintf($theme ? '(%s)' : '%s', [
            Html::encode(Yii::t('app-rule3', $role->name)),
          ])
          : null,
      ],
      fn (?string $v): bool => $v !== null,
    ),
  );
} else {
  echo Html::encode(Yii::t('app', $ourTeam ? 'Good Guys' : 'Bad Guys'));
}
