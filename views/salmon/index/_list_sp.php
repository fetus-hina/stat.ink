<?php
declare(strict_types=1);

use app\assets\AppOptAsset;
use app\assets\RpgAwesomeAsset;
use app\components\i18n\Formatter;
use app\models\Salmon2;
use yii\helpers\Html;
use yii\widgets\ListView;

RpgAwesomeAsset::register($this);
AppOptAsset::register($this)
  ->registerCssFile($this, 'battles-simple.css');
?>
<div class="text-center">
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => [ 'tag' => false ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ],
  ]) . "\n" ?>
</div>
<p>
  <?= Html::a(
    '<span class="fa fa-list fa-fw"></span> ' . Html::encode(Yii::t('app', 'Detailed List')),
    array_merge(
      [], // $filter->toQueryParams(),
      ['salmon/index',
        'screen_name' => $user->screen_name,
        'v' => 'standard',
      ]
    ),
    ['class' => 'btn btn-default', 'rel' => 'nofollow']
  ) . "\n" ?>
</p>
<div id="battles">
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'options' => [
      'tag' => 'ul',
      'class' => 'list-view simple-battle-list',
    ],
    'itemOptions' => [
      'tag' => 'li',
      'class' => 'simple-battle-row',
    ],
    'itemView' => '_list_sp_item',
    'viewParams' => [
      'user' => $user,
      'formatter' => Yii::createObject([
        'class' => Formatter::class,
        'nullDisplay' => '?',
      ]),
    ],
  ]) . "\n" ?>
</div>
<div class="text-center">
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => [ 'tag' => false ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ],
  ]) . "\n" ?>
</div>
