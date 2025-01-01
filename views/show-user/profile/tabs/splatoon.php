<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\battle\PanelListWidget;
use app\models\Battle;
use app\models\User;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

?>
<?= $this->render('//includes/battles-summary', [
  'summary' => Battle::find()
    ->with('user')
    ->andWhere(['user_id' => $user->id])
    ->getSummary(),
  'link' => ['show/user', 'screen_name' => $user->screen_name],
]) . "\n" ?>
<div class="row">
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-rule', 'Turf War'),
      'titleLink' => ['/show/user',
        'screen_name' => $user->screen_name,
        'filter' => [
          'rule' => 'nawabari'
        ],
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle::find()
        ->andWhere(['user_id' => $user->id])
        ->with(['user', 'map', 'weapon'])
        ->innerJoinWith(['lobby', 'rule'])
        ->andWhere(['rule.key' => 'nawabari'])
        ->orderBy(['battle.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-rule', 'Ranked Battle'),
      'titleLink' => ['/show/user',
        'screen_name' => $user->screen_name,
        'filter' => [
          'rule' => '@gachi'
        ],
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle::find()
        ->andWhere(['user_id' => $user->id])
        ->with(['user', 'map', 'weapon'])
        ->innerJoinWith(['lobby', 'rule'])
        ->andWhere(['rule.key' => ['area', 'yagura', 'hoko']])
        ->orderBy(['battle.id' => SORT_DESC])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
</div><!-- row -->
