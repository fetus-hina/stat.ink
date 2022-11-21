<?php

declare(strict_types=1);

use app\models\Salmon3;
use app\models\User;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var Salmon3 $model
 * @var View $this
 */

$battleDetails = __DIR__ . '/../../show-v3/battle/details';

echo DetailView::widget([
  'model' => $model,
  'id' => 'battle',
  'options' => [
    'class' => ['table', 'table-striped'],
  ],
  'template' => function (array $attribute, $index, Widget $widget): ?string {
    if ($attribute['value'] === null) {
      return null;
    }
    $captionOptions = Html::renderTagAttributes(
      ArrayHelper::getValue($attribute, 'captionOptions', [])
    );
    $contentOptions = Html::renderTagAttributes(
      ArrayHelper::getValue($attribute, 'contentOptions', [])
    );
    return strtr(
      '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>',
      [
        '{label}' => $attribute['label'],
        '{value}' => $widget->formatter->format($attribute['value'], $attribute['format']),
        '{captionOptions}' => $captionOptions,
        '{contentOptions}' =>  $contentOptions,
      ]
    );
  },
  'attributes' => [
    require __DIR__ . '/details/schedule.php',
    require __DIR__ . '/details/map.php',
    require __DIR__ . '/details/hazard-level.php',
    require __DIR__ . '/details/king-smell.php',
    require __DIR__ . '/details/result.php',
    require __DIR__ . '/details/title.php',
    require __DIR__ . '/details/job-point.php',
    require __DIR__ . '/details/salmon-eggs.php',
    require __DIR__ . '/details/fish-scales.php',
    require __DIR__ . '/details/link-url.php',
    require __DIR__ . '/details/start-at.php',
    require __DIR__ . '/details/end-at.php',
    require __DIR__ . '/details/created-at.php',
    require __DIR__ . '/details/user-agent.php',
    require __DIR__ . '/details/user-agent-variables.php',
    require __DIR__ . '/details/public-note.php',
    require __DIR__ . '/details/private-note.php',
    require __DIR__ . '/details/game-version.php',
  ],
]);
