<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Rule3;
use app\models\Season3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3[] $rules
 * @var Season3 $season,
 * @var User $user
 * @var View $this
 */

?>
<thead>
  <tr>
    <th colspan="2" rowspan="2" class="text-center"><?= Html::encode(Yii::t('app', 'Date')) ?></th>
<?php foreach ($rules as $rule) { ?>
    <?= trim($this->render('thead/rule', compact('rule', 'season', 'user'))) . "\n" ?>
<?php } ?>
  </tr>
  <tr>
<?php foreach ($rules as $rule) { ?>
    <th class="text-center"><?= Icon::number() ?></th>
    <th class="text-center"><?= Icon::lowerBound() ?></th>
    <th class="text-center"><?= Icon::upperBound() ?></th>
<?php } ?>
  </tr>
</thead>
