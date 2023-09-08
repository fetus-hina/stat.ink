<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BattlePlayer3|BattleTricolorPlayer3 $player
 * @var View $this
 */

$f = Yii::$app->formatter;

if ($player->is_crowned || $player->crown) {
  echo Html::tag(
    'div',
    Icon::s3Crown($player->crown ?? 'x'),
    [
      'class' => 'small',
      'style' => [
        'line-height' => '1',
      ],
    ],
  );
}

// TODO: blackout / anonymize
$title = $player->splashtagTitle;
if ($title) {
  echo Html::tag(
    'div',
    Html::encode((string)$title->name),
    ['class' => 'small text-muted'],
  );
}

echo Html::tag(
  'div',
  trim(
    vsprintf('%s %s %s', [
      Icon::s3Species($player->species),
      Html::encode($player->name),
      $player->number !== null
        ? Html::tag(
          'span',
          sprintf('#%s', $player->number),
          ['class' => 'text-muted small'],
        )
        : '',
    ]),
  ),
);
