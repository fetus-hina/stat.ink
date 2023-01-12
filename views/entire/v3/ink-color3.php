<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\StatInkColor3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatInkColor3[] $models
 * @var View $this
 */

$title = Yii::t('app', 'Ink Color');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('ink-color3/table', compact('models')) . "\n" ?>
  <?= $this->render('ink-color3/chart', compact('models')) . "\n" ?>
</div>
