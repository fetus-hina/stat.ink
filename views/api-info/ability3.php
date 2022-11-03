<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\CcBy;
use app\components\widgets\SnsWidget;
use app\models\Ability3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Ability3[] $abilities
 * @var View $this
 * @var array[] $langs
 */

$this->context->layout = 'main';
$this->title = Yii::t('app', 'API Info: Abilities (Splatoon 3)');

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
  <p>
    <?= implode(' ', [
      Html::a(
        implode('', [
          Html::tag('span', '', ['class' => ['fas fa-file-code fa-fw']]),
          Html::encode(Yii::t('app', 'JSON format')),
        ]),
        ['api-v3/ability'],
        ['class' => 'label label-default']
      ),
      Html::a(
        implode('', [
          Html::tag('span', '', ['class' => ['fas fa-file-code fa-fw']]),
          Html::encode(Yii::t('app', 'JSON format (All langs)')),
        ]),
        ['api-v3/ability', 'full' => 1],
        ['class' => 'label label-default']
      ),
    ]) . "\n" ?>
  </p>
  <?= $this->render('ability3/list', ['langs' => $langs, 'abilities' => $abilities]) . "\n" ?>
  <hr>
  <?= CcBy::widget() . "\n" ?>
</div>
