<?php

/**
 * @copyright Copyright (C) 2021-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $data
 */

$maps = $data['rules'][$rule['key']]['maps'] ?? null;
if (!$maps) {
  return;
}

var_dump($maps);

?>
<div class="row">
</div>
