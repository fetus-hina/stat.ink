<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\CcBy;
use app\components\widgets\SnsWidget;
use app\models\Language;
use app\models\SalmonBoss3;
use app\models\SalmonKing3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var SalmonBoss3[] $bosses
 * @var SalmonKing3[] $kings
 * @var View $this
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Boss Salmonids (Splatoon 3)');

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $this->title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= $this->render('boss-salmonid3/boss', ['langs' => $langs, 'salmonids' => $bosses]) . "\n" ?>
  <?= $this->render('boss-salmonid3/king', ['langs' => $langs, 'salmonids' => $kings]) . "\n" ?>
  <hr>
  <?= CcBy::widget() . "\n" ?>
</div>
