<?php

declare(strict_types=1);

use app\assets\FlexboxAsset;
use app\components\widgets\Icon;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var View $this
 */

FlexboxAsset::register($this);

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

echo Html::tag(
  'div',
  implode('', [
    Html::a(
      implode(' ', [
        Icon::fileCsv(),
        Html::encode(Yii::t('app', 'Salmon Run CSV')),
        Html::tag('small', Html::encode('(β)')),
      ]),
      ['download-salmon', 'type' => 'csv'],
      ['class' => 'btn btn-default text-left-important flex-grow-1'],
    ),
    Html::a(
      Icon::help(),
      'https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/export-salmon-csv.md',
      [
        'class' => 'btn btn-default auto-tooltip',
        'rel' => 'external noopener',
        'target' => '_blank',
        'title' => Yii::t('app', 'Schema information'),
      ],
    ),
  ]),
  ['class' => 'btn-group d-flex'],
);
