<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\BattleSummarizer;
use app\components\widgets\battle\PanelListWidget;
use app\models\Battle3;
use app\models\Rule3;
use app\models\RuleGroup3;
use app\models\Salmon3;
use app\models\User;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
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
      'title' => Yii::t('app', 'Battles'),
      'titleLink' => ['/show-v3/user',
        'screen_name' => $user->screen_name,
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'is_deleted' => false,
          'user_id' => $user->id,
        ])
        ->with([
            'lobby',
            'map',
            'result',
            'rule',
            'user',
            'weapon',
            'weapon.special',
            'weapon.subweapon',
        ])
        ->orderBy([
            '{{%battle3}}.[[end_at]]' => SORT_DESC,
            '{{%battle3}}.[[id]]' => SORT_DESC,
        ])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
  <div class="col-xs-12 col-sm-6">
    <?= PanelListWidget::widget([
      'title' => Yii::t('app-salmon2', 'Salmon Run'),
      'titleLink' => ['/salmon-v3/index',
        'screen_name' => $user->screen_name,
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Salmon3::find()
        ->with([
          'bigStage',
          'kingSalmonid',
          'stage',
          'titleAfter',
          'user',
          'salmonPlayer3s' => function (ActiveQuery $query): void {
            $query->onCondition(['{{%salmon_player3}}.[[is_me]]' => true]);
          },
        ])
        ->andWhere([
          'is_deleted' => false,
          'user_id' => $user->id,
        ])
        ->orderBy([
          '{{%salmon3}}.[[start_at]]' => SORT_DESC,
          '{{%salmon3}}.[[id]]' => SORT_DESC,
        ])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
</div><!-- row -->
