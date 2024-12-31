<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3TideEvent;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatSalmon3TideEvent[] $stats
 * @var View $this
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonWaterLevel2> $tides
 */

?>
<?= Html::beginTag('table', [
  'class' => ['m-0', 'nobr', 'table', 'table-bordered', 'table-striped'],
]) . "\n" ?>
  <?= $this->render('table/header', compact('tides')) . "\n" ?>
  <tbody>
    <?= $this->render('table/row', [
      'tides' => $tides,
      'stats' => $stats,
      'event' => '*',
    ]) . "\n" ?>
    <?= $this->render('table/row', [
      'tides' => $tides,
      'stats' => $stats,
      'event' => null,
    ]) . "\n" ?>
<?php foreach ($events as $event) { ?>
    <?= $this->render('table/row', compact('event', 'tides', 'stats')) . "\n" ?>
<?php } ?>
  </tbody>
</table>
