<?php

declare(strict_types=1);

use app\components\helpers\BattleSummarizer;
use app\components\widgets\battle\PanelListWidget;
use app\models\Battle3;
use app\models\Rule3;
use app\models\User;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

$nawabari = Rule3::findOne(['key' => 'nawabari']);
if (!$nawabari) {
  throw new LogicException();
}

$gachiIds = array_map(
  fn (Rule3 $rule): int => (int)$rule->id,
  Rule3::find()->andWhere(['key' => ['area', 'yagura', 'hoko', 'asari']])->all()
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
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'is_deleted' => false,
          'rule_id' => $nawabari->id,
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
      ],
      'titleLinkText' => Yii::t('app', 'List'),
      'models' => Battle3::find()
        ->andWhere([
          'is_deleted' => false,
          'rule_id' => $gachiIds,
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
