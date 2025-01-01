<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\OverfishingTrait;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\web\View;

/**
 * @phpstan-import-type WaveStats from OverfishingTrait
 *
 * @var Formatter $fmt
 * @var SalmonEvent3|null $event
 * @var View $this
 * @var WaveStats[] $stats
 * @var array<int, SalmonWaterLevel2> $tides
 * @var string $modalId
 */

if (!$stats) {
  return;
}

?>
<?php foreach (array_values($stats) as $i => $stat) { ?>
<tr>
<?php if ($i === 0) { ?>
  <?= Html::tag(
    'td',
    Html::encode(
      $event
        ? Yii::t('app-salmon-event3', $event->name)
        : Yii::t('app-salmon-overfishing', 'Day Waves'),
    ),
    [
      'rowspan' => (string)count($stats),
      'class' => 'text-center'
    ],
  ) . "\n" ?>
<?php } ?>
  <?= Html::tag(
    'td',
    implode(' ', [
      Icon::s3SalmonTide(
        TypeHelper::string(ArrayHelper::getValue($tides, [$stat['tide_id'], 'key'])),
      ),
      Html::encode(
        Yii::t(
          'app-salmon-tide2',
          TypeHelper::string(ArrayHelper::getValue($tides, [$stat['tide_id'], 'name'])),
        ),
      ),
    ]),
    ['class' => 'text-center'],
  ) . "\n" ?>
  <?= Html::tag(
    'td',
    implode(' ', [
      Icon::goldenEgg(),
      $fmt->asInteger($stat['golden_eggs']),
    ]),
    ['class' => 'text-center'],
  ) . "\n" ?>
</tr>
<?php } ?>
