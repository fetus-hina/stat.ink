<?php

declare(strict_types=1);

use app\components\helpers\BattleSummarizer;
use app\components\widgets\battle\PanelListWidget;
use app\models\Battle3;
use app\models\User;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

?>
<?= $this->render('//includes/battles-summary', [
  'summary' => BattleSummarizer::getSummary3(
    Battle3::find()
      ->joinWith(['result', 'user'])
      ->andWhere([
        'user_id' => $user->id,
        'is_deleted' => false,
      ])
  ),
  'link' => ['show-v3/user', 'screen_name' => $user->screen_name],
]) . "\n" ?>
<div class="row">
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-rule3', 'Turf War'),
      'titleLink' => ['/show-v3/user',
        'screen_name' => $user->screen_name,
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'user_id' => $user->id,
          'is_deleted' => false,
        ])
        ->with(['lobby', 'map', 'result', 'user', 'weapon'])
        ->innerJoinWith(['rule'])
        ->andWhere(['rule3.key' => 'nawabari'])
        ->orderBy(['battle3.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-lobby3', 'Anarchy Battle'),
      'titleLink' => ['/show-v3/user',
        'screen_name' => $user->screen_name,
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'user_id' => $user->id,
          'is_deleted' => false,
        ])
        ->with(['lobby', 'map', 'result', 'user', 'weapon'])
        ->innerJoinWith(['rule'])
        ->andWhere(['<>', 'rule3.key', 'nawabari'])
        ->orderBy(['battle3.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
</div><!-- row -->
