<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Mode2;
use app\models\Rule2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $data
 */

$rules = ArrayHelper::sort(
  ArrayHelper::getColumn(
    Mode2::findOne(['key' => 'gachi'])->rules,
    fn (Rule2 $rule): array => $rule->attributes,
  ),
  fn (array $a, array $b): int => $a['id'] <=> $b['id'],
);

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
