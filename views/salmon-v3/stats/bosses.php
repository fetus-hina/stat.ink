<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Salmon3FilterWidget;
use app\components\widgets\SalmonUserInfo3;
use app\components\widgets\SnsWidget;
use app\models\Salmon3FilterForm;
use app\models\SalmonBoss3;
use app\models\User;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Salmon3FilterForm $filter
 * @var User $user
 * @var View $this
 * @var array<string, SalmonBoss3> $bosses
 * @var array<string, array{boss_key: string, appearances: int, defeated: int, defeated_by_me: int}> $stats
 * @var array{type: string, key: string, name: string, defeated: int}[] $badges
 */

$permLink = Url::to(
  array_merge(
    $filter->toPermLink(),
    ['salmon-v3/stats-bosses', 'screen_name' => $user->screen_name],
  ),
  true,
);

$title = Yii::t('app-salmon3', "{name}'s Salmon Stats (Bosses)", [
  'name' => $user->name,
]);

$this->title = sprintf('%s | %s', Yii::$app->name, $title);

$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag([
  'name' => 'twitter:image',
  'content' => $user->iconUrl,
]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
}

SortableTableAsset::register($this);

?>
<div class="container">
  <span itemscope itemtype="http://schema.org/BreadcrumbList">
    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <?= Html::tag('meta', '', ['itemprop' => 'url', 'content' => Url::home(true)]) . "\n" ?>
      <?= Html::tag('meta', '', ['itemprop' => 'title', 'content' => Yii::$app->name]) . "\n" ?>
    </span>
  </span>
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget([]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <?= Yii::$app->cache->getOrSet(
        [
            2, // cache version
            __FILE__,
            __LINE__,
            Yii::$app->language,
            $user->id,
            $stats,
            ArrayHelper::getValue(Yii::$app->params, 'assetRevision'),
        ],
        fn (): string => $this->render('bosses/table', [
          'bosses' => $bosses,
          'stats' => $stats,
          'user' => $user,
        ]),
        3600,
      ) . "\n" ?>
      <?= $this->render('bosses/badge', [
        'badges' => $badges,
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= Salmon3FilterWidget::widget(['filter' => $filter, 'user' => $user]) . "\n" ?>
      <?= SalmonUserInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
