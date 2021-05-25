<?php

declare(strict_types=1);

use app\models\Mode2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $data
 */

static $rules = null;
if ($rules === null) {
  $rules = Mode2::findOne(['key' => 'gachi'])
    ->getRules()
    ->orderBy(['id' => SORT_ASC])
    ->asArray()
    ->all();
}

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
