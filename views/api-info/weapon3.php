<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\CcBy;
use app\components\widgets\SnsWidget;
use app\models\Language;
use app\models\Special3;
use app\models\Subweapon3;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var Special3[] $specials
 * @var Subweapon3[] $subs
 * @var View $this
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Weapons (Splatoon 3)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('weapon3/sub', ['langs' => $langs, 'subs' => $subs]) . "\n" ?>
  <?= $this->render('weapon3/special', ['langs' => $langs, 'specials' => $specials]) . "\n" ?>
  <hr>
  <?= CcBy::widget() . "\n" ?>
</div>
