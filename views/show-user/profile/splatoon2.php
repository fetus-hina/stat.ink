<?php

declare(strict_types=1);

use app\components\helpers\BattleSummarizer;
use app\components\widgets\battle\PanelListWidget;
use app\models\Battle2;
use app\models\Salmon2;
use app\models\User;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

?>
<?= $this->render('@app/views/includes/battles-summary', [
  'summary' => Battle2::find()->andWhere(['user_id' => $user->id])->getSummary(),
  'link' => ['show-v2/user', 'screen_name' => $user->screen_name],
]) . "\n" ?>
<div class="row">
<?php if (Salmon2::find()->andWhere(['user_id' => $user->id])->exists()) { ?>
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app', 'Battles'),
      'titleLink' => ['/show-v2/user', 'screen_name' => $user->screen_name],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle2::find()
        ->andWhere(['user_id' => $user->id])
        ->with(['user', 'map', 'weapon'])
        ->innerJoinWith(['mode', 'rule'])
        ->orderBy(['battle2.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-salmon2', 'Salmon Run'),
      'titleLink' => ['salmon/index', 'screen_name' => $user->screen_name],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Salmon2::find()
        ->andWhere(['user_id' => $user->id])
        ->with(['players', 'stage', 'user'])
        ->orderBy(['id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
<?php } else { ?>
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-rule2', 'Turf War'),
      'titleLink' => ['/show-v2/user',
        'screen_name' => $user->screen_name,
        'filter' => [
          'rule' => 'standard-regular-nawabari',
        ],
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle2::find()
        ->andWhere(['user_id' => $user->id])
        ->with(['user', 'map', 'weapon'])
        ->innerJoinWith(['mode', 'rule'])
        ->andWhere(['rule2.key' => 'nawabari'])
        ->orderBy(['battle2.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-rule2', 'Ranked Battle'),
      'titleLink' => ['/show-v2/user',
        'screen_name' => $user->screen_name,
        'filter' => [
          'rule' => 'any-gachi-any',
        ],
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle2::find()
        ->andWhere(['user_id' => $user->id])
        ->with(['user', 'map', 'weapon'])
        ->innerJoinWith(['mode', 'rule'])
        ->andWhere(['<>', '{{rule2}}.[[key]]', 'nawabari'])
        ->orderBy(['battle2.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
<?php } ?>
</div><!-- row -->
