<?php

use app\components\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$list = [
  'ko' => Yii::t('app', 'Knockout'),
  'time' => Yii::t('app', 'Time is up'),
];

$this->registerJs('$(".legend-bg").each(function(){$(this).css("background-color", window.colorScheme[$(this).attr("data-color")])});');

?>
<?= Html::tag(
  'div',
  implode('', array_map(
    function ($color, $text) : string {
      return Html::tag(
        'div',
        sprintf(
          '%s %s',
          Html::tag('span', '', [
            'style' => [
              'display' => 'inline-block',
              'width' => '1.618em',
              'height' => '1em',
              'line-height' => '1px',
            ],
            'class' => 'legend-bg',
            'data' => [
              'color' => $color,
            ],
         ]),
         Html::encode($text)
        )
      );
    },
    array_keys($list),
    array_values($list)
  )),
  ['style' => [
    'display' => 'inline-block',
    'border' => '2px solid #ddd',
    'padding' => '2px 5px',
  ]]
) ?>
