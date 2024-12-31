<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\AboutAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
  Yii::$app->name,
  'About Translation',
]);

$this->context->layout = 'main';
$this->title = $title;

AboutAsset::register($this);
?>
<div class="container" lang="en">
  <h1>
    About Translation
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2>
    As Of Today
  </h2>
  <p>
    This website supports Japanese and English.
    But, there are some pages that has not been translated.
    And there is a lot of my bad English.
    Sorry for inconvinience.
  </p>

  <h2>
    Need Help
  </h2>
  <p>
    I'm looking for translation and/or proofreading volunteer staff.
    Please contact me if you help me.
  </p>
</div>
