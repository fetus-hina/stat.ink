<?php

declare(strict_types=1);

use app\components\helpers\Html;
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
