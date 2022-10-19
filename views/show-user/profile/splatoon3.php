<?php

declare(strict_types=1);

use app\components\helpers\BattleSummarizer;
use app\components\widgets\battle\PanelListWidget;
use app\models\Battle3;
use app\models\Rule3;
use app\models\RuleGroup3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

/**
 * @var array<string, array<string, int>>
 */
$ruleIds = ArrayHelper::map(
  Rule3::find()->with('group')->all(),
  'key',
  'id',
  fn (Rule3 $model): string => $model->group->key,
);

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
        'f' => [
          'rule' => 'nawabari',
        ],
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'is_deleted' => false,
          'rule_id' => array_values($ruleIds['nawabari']),
          'user_id' => $user->id,
        ])
        ->with(['lobby', 'map', 'result', 'rule', 'user', 'weapon', 'weapon.subweapon', 'weapon.special'])
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
      'title' => Yii::t('app-lobby3', 'Anarchy Battle'),
      'titleLink' => ['/show-v3/user',
        'screen_name' => $user->screen_name,
        'f' => [
          'lobby' => '@bankara',
        ],
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'is_deleted' => false,
          'rule_id' => array_values($ruleIds['gachi']),
          'user_id' => $user->id,
        ])
        ->with(['lobby', 'map', 'result', 'rule', 'user', 'weapon', 'weapon.subweapon', 'weapon.special'])
        ->orderBy([
            '{{%battle3}}.[[end_at]]' => SORT_DESC,
            '{{%battle3}}.[[id]]' => SORT_DESC,
        ])
        ->limit(5)
        ->all(),
    ]) . "\n" ?>
  </div><!-- col -->
</div><!-- row -->
