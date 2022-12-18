<?php

declare(strict_types=1);

use app\models\Battle3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $model
 * @var View $this
 */

$this->registerCss('.cell-judge .progress{width:100%;min-width:150px;margin-bottom:0}');

$draw = function (
  float $ourValue,
  float $theirValue,
  ?float $thirdValue,
  string $ourString,
  string $theirString,
  ?string $thirdString,
  ?string $ourTitle = null,
  ?string $theirTitle = null,
  ?string $thirdTitle = null,
  ?string $ourColor = null,
  ?string $theirColor = null,
  ?string $thirdColor = null,
): string {
  $bar = function (
    ?float $curValue,
    ?float $otherValue1,
    ?float $otherValue2,
    string $class,
    string $string,
    ?string $title = null,
    ?string $color = null,
  ): string {
    return ($curValue > 0)
      ? Html::tag('div', Html::encode($string), [
        'class' => [
          'progress-bar',
          $class,
          $title === null ? '' : 'auto-tooltip',
        ],
        'style' => array_merge(
          [
            'width' => sprintf('%.f%%', $curValue * 100 / ($curValue + $otherValue1 + $otherValue2)),
            'font-weight' => ($curValue === max($curValue, $otherValue1, $otherValue2))
              ? '700'
              : '400',
          ],
          // $color ? ['background-color' => "#{$color}"] : [],
          [],
        ),
        'title' => $title ?? '',
      ])
      : '';
  };
  return Html::tag(
    'div',
    implode('', [
      $ourValue > 0
        ? $bar($ourValue, $theirValue, $thirdValue, 'progress-bar-info', $ourString, $ourTitle, $ourColor)
        : '',
      $theirValue > 0
        ? $bar($theirValue, $ourValue, $thirdValue, 'progress-bar-danger', $theirString, $theirTitle, $theirColor)
        : '',
      $thirdValue > 0
        ? $bar($thirdValue, $ourValue, $theirValue, 'progress-bar-warning', $thirdString, $thirdTitle, $thirdColor)
        : '',
    ]),
    ['class' => 'progress'],
  );
};

$fmt = Yii::$app->formatter;
$isTricolor = $model->rule?->key === 'tricolor';

if ($model->our_team_percent !== null && $model->their_team_percent !== null) {
  $ourTitle = null;
  $theirTitle = null;
  $thirdTitle = null;
  if (
    $model->our_team_inked !== null &&
    $model->their_team_inked !== null
  ) {
    $ourTitle = Yii::t('app', '{point}p', [
      'point' => $fmt->asInteger((int)$model->our_team_inked),
    ]);
    $theirTitle = Yii::t('app', '{point}p', [
      'point' => $fmt->asInteger((int)$model->their_team_inked),
    ]);
    $thirdTitle = Yii::t('app', '{point}p', [
      'point' => $fmt->asInteger((int)$model->third_team_inked),
    ]);
  } elseif ($model->map && $model->map->area !== null) {
    $ourTitle = Yii::t('app', '~{point}p', [
      'point' => $fmt->asInteger(round(
        $model->our_team_percent * $model->map->area / 100
      )),
    ]);
    $theirTitle = Yii::t('app', '~{point}p', [
      'point' => $fmt->asInteger(round(
        $model->their_team_percent * $model->map->area / 100
      )),
    ]);
    $thirdTitle = Yii::t('app', '~{point}p', [
      'point' => $fmt->asInteger(round(
        $model->third_team_percent * $model->map->area / 100
      )),
    ]);
  }
  echo $draw(
    (float)$model->our_team_percent,
    (float)$model->their_team_percent,
    $isTricolor ? (float)$model->third_team_percent : null,
    $fmt->asPercent($model->our_team_percent / 100, 1),
    $fmt->asPercent($model->their_team_percent / 100, 1),
    $isTricolor ? $fmt->asPercent($model->third_team_percent / 100, 1) : null,
    $ourTitle,
    $theirTitle,
    $thirdTitle,
    $model->our_team_color,
    $model->their_team_color,
    $model->third_team_color,
  ) . "\n";
} elseif (
  $model->our_team_inked !== null &&
  $model->their_team_inked !== null
) {
  echo $draw(
    (float)$model->our_team_inked,
    (float)$model->their_team_inked,
    $isTricolor ? (float)$model->third_team_inked : null,
    Yii::t('app', '{point}p', [
      'point' => $fmt->asInteger((int)$model->our_team_inked),
    ]),
    Yii::t('app', '{point}p', [
      'point' => $fmt->asInteger((int)$model->their_team_inked),
    ]),
    $isTricolor
      ? Yii::t('app', '{point}p', [
        'point' => $fmt->asInteger((int)$model->third_team_inked),
      ])
      : null,
    null,
    null,
    null,
    $model->our_team_color,
    $model->their_team_color,
    $model->third_team_color,
  ) . "\n";
} elseif ($model->our_team_count !== null && $model->their_team_count !== null) {
  echo $draw(
    (float)$model->our_team_count,
    (float)$model->their_team_count,
    null,
    $model->our_team_count == 100
      ? Yii::t('app', 'KNOCKOUT')
      : (string)$model->our_team_count,
    $model->their_team_count == 100
      ? Yii::t('app', 'KNOCKOUT')
      : (string)$model->their_team_count,
    null,
    null,
    null,
    $model->our_team_color,
    $model->their_team_color,
    null,
  ) . "\n";
}
