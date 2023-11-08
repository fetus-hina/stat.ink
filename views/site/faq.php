<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\MarkdownRendererWidget;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
    Yii::$app->name,
    Yii::t('app', 'FAQ'),
]);

$this->title = $title;
TypeHelper::instanceOf($this->context, Controller::class)->layout = 'main'

?>
<div id="faq-container" class="container">
  <h1>
    <?= Html::encode(Yii::t('app', 'FAQ')) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="alert alert-warning mb-3">
    <p>
      <?= Yii::t('app', 'Please refer to the respective projects for any problems or questions regarding the operation of each application.') . "\n" ?>
    </p>
    <ul>
      <li>
        <?= Icon::splatoon3() . "\n" ?>
        <a class="alert-link" href="https://github.com/frozenpandaman/s3s#readme">s3s</a>
      </li>
      <li>
        <?= Icon::splatoon3() . "\n" ?>
        <a class="alert-link" href="https://github.com/spacemeowx2/s3si.ts#readme">s3si.ts</a>
      </li>
      <li>
        <?= Icon::splatoon2() . "\n" ?>
        <a class="alert-link" href="https://github.com/frozenpandaman/splatnet2statink#readme">splatnet2statink</a>
      </li>
      <li>
        <?= Icon::splatoon1() . "\n" ?>
        <a class="alert-link" href="https://github.com/hasegaw/IkaLog">IkaLog</a>
        (<a class="alert-link" href="https://github.com/hasegaw/IkaLog/wiki/ja_FAQ">FAQ</a>)
      </li>
    </ul>
  </div>

  <?= MarkdownRendererWidget::widget([
    'basedir' => __DIR__,
    'filename' => 'faq.{lang}.md',
  ]) . "\n" ?>
</div>
