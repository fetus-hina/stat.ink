<?php

declare(strict_types=1);

use app\assets\SimpleBattleListAsset;
use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var User $user
 * @var View $this
 */

SimpleBattleListAsset::register($this);

?>
<div class="text-center">
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => [
      'tag' => false,
    ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ],
  ]) . "\n" ?>
</div>
<?php /* ?>
<?= $this->render('_summary', [
  'summary' => $dataProvider->query->summary(),
]) . "\n" ?>
<?php */ ?>
<p>
  <?= Html::a(
    implode(' ', [
      Icon::list(),
      Html::encode(Yii::t('app', 'Detailed List')),
    ]),
    array_merge(
      [], // $filter->toQueryParams(),
      ['salmon-v3/index',
        'screen_name' => $user->screen_name,
        'v' => 'standard',
      ]
    ),
    [
      'class' => 'btn btn-default',
      'rel' => 'nofollow',
    ],
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
    'itemView' => 'sp/item',
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
    'itemOptions' => [
      'tag' => false,
    ],
    'layout' => '{pager}',
    'pager' => [
      'maxButtonCount' => 5
    ],
  ]) . "\n" ?>
</div>
