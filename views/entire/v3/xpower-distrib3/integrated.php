<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Rule3;
use app\models\StatXPowerDistribAbstract3;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array<int, StatXPowerDistribAbstract3> $abstracts
 */

?>
<div class="mb-4">
  <?= $this->render('integrated/abstracts', compact('abstracts', 'rules')) . "\n" ?>
  <?= $this->render('integrated/histogram', compact('abstracts', 'rules')) . "\n" ?>
</div>
