<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Special3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Special3 $special
 * @var View $this
 * @var array<int, array{battles: int, wins: int}> $data
 */

echo $this->render('../includes/rule-header', ['id' => true, 'rule' => $rule]);

if (!$data) {
  echo Html::tag('p', Html::encode(Yii::t('app', 'No Data')), ['class' => 'mb-3']);
  return;
}

?>
<div class="row">
  <div class="col-xs-12 col-md-7 col-lg-8 mb-3">
    <?= $this->render('rule/chart', compact('data')) . "\n" ?>
  </div>
  <div class="col-xs-12 col-md-5 col-lg-4 mb-3">
    <?= $this->render('rule/table', compact('data', 'special')) . "\n" ?>
  </div>
</div>
