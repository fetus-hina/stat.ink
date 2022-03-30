<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\models\Mode2;
use app\models\ModeRule2;
use app\models\Rule2;
use app\components\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var array|null $rules
 */
static $rules = null;
if ($rules === null) {
  if (!$mode = Mode2::findOne(['key' => 'gachi'])) {
      throw new RuntimeException();
  }

  $ruleIdList = ArrayHelper::getColumn(
    ModeRule2::find()
      ->andWhere(['mode_id' => $mode->id])
      ->asArray()
      ->all(),
    'rule_id',
  );

  $rules = Rule2::find()
    ->andWhere(['id' => $ruleIdList])
    ->orderBy(['id' => SORT_ASC])
    ->asArray()
    ->all();

  unset($ruleIdList);
  unset($mode);
}

/**
 * @var View $this
 * @var array $data
 */

?>
<div class="row">
<?php foreach ($rules as $rule) { ?>
  <div class="col-12 col-md-6 col-lg-3 mb-3">
    <?= $this->render('//show-v2/user-stat-monthly-report/summarized-rule', [
      'rule' => $rule,
      'maps' => $data['rules'][$rule['key']]['maps'] ?? null,
    ]) . "\n" ?>
  </div>
<?php } ?>
</div>
