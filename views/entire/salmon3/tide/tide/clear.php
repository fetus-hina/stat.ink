<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\SalmonWaterLevel2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $info
 * @var array<int, SalmonWaterLevel2> $tides
 */

?>
<table class="table table-striped mb-2">
  <thead>
    <tr>
      <th width="45%" class="text-center omit"><?= Html::encode(Yii::t('app-salmon-tide2', 'Water Level')) ?></th>
      <th width="55%" class="text-center omit"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($tides as $tideId => $tide) { ?>
    <tr>
      <?= Html::tag(
        'th',
        implode(' ', [
          Icon::s3SalmonTide($tide),
          Html::encode(Yii::t('app-salmon-tide2', $tide->name)),
        ]),
        [
          'class' => 'text-center',
          'scope' => 'row',
        ],
      ) . "\n" ?>
      <?= Html::tag(
        'td',
        Html::encode(
          isset($info['tides'][$tideId]) && isset($info['clears'][$tideId]) && $info['tides'][$tideId] > 0
            ? Yii::$app->formatter->asPercent($info['clears'][$tideId] / $info['tides'][$tideId], 2)
            : '',
        ),
        ['class' => 'text-center'],
      ) . "\n" ?>
    </tr>
<?php } ?>
    <tr>
      <?= Html::tag(
        'th',
        Html::encode(Yii::t('app', 'Total')),
        [
          'class' => 'text-center',
          'scope' => 'row',
        ],
      ) . "\n" ?>
      <?= Html::tag(
        'td',
        Html::encode(
          $info['total'] > 0
            ? Yii::$app->formatter->asPercent($info['clear'] / $info['total'], 2)
            : '',
        ),
        ['class' => 'text-center'],
      ) . "\n" ?>
    </tr>
  </tbody>
</table>
