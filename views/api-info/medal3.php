<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\CcBy;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\MedalCanonical3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var MedalCanonical3[] $medals
 * @var View $this
 * @var array[] $langs
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Medals (Splatoon 3)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

?>
<div class="container">
  <h1>
    <?= Html::encode($this->title) . "\n" ?>
  </h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
  <?= $this->render('medal3/list', compact('langs', 'medals')) . "\n" ?>
  <hr>
  <?= CcBy::widget() . "\n" ?>
</div>
