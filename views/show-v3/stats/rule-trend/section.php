<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\show\v3\stats\RuleTrendAction;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<string, mixed> $data
 * @var bool $isRanked
 * @var string $charts
 * @var string $id
 * @var string $title
 */

echo Html::tag('h2', Html::a(Html::encode($title), '#' . $id), ['id' => $id]) . "\n";

echo $this->render('chart', [
  'isRanked' => $isRanked,
  'showPerBattle' => !$data['thinned'],
  'kind' => $charts,
  'series' => $data['all'],
]) . "\n";

if ($data['recent']) {
  echo Html::tag(
    'h3',
    Html::encode(Yii::t('app', 'Last {n} Battles', ['n' => RuleTrendAction::RECENT_BATTLES])),
  ) . "\n";
  echo $this->render('chart', [
    'isRanked' => $isRanked,
    'showPerBattle' => true,
    'kind' => $charts,
    'series' => $data['recent'],
  ]) . "\n";
}
