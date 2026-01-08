<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Rule3;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use app\models\StatWeapon3XUsagePerVersion;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]|StatWeapon3XUsage[]|StatWeapon3XUsagePerVersion[] $data
 * @var View $this
 */

$fmt = Yii::$app->formatter;

$totalSamples = array_sum(
  array_map(
    fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $row): int => $row->battles,
    $data,
  ),
);

if ($totalSamples < 1) {
  return;
}

$totalWins = array_sum(
  array_map(
    fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage|StatWeapon3XUsagePerVersion $row): int => $row->wins,
    $data,
  ),
);

$winRate = $totalWins / $totalSamples;
$error = ($winRate - 0.5) * 100;
?>
<div class="mb-3">
  <?= implode('', [
    ($rule->key === 'nawabari')
      ? Html::tag(
        'p',
        Html::encode(
          Yii::t('app', 'Aggregated: {rules}', [
            'rules' => implode(', ', [
              Yii::t('app-lobby3', 'Regular Battle'),
              Yii::t('app-lobby3', 'Splatfest (Pro)'),
            ]),
          ]),
        ),
        ['class' => 'mb-1'],
      )
      : '',
    Html::tag(
      'p',
      Html::encode(
        Yii::t('app', 'Aggregated: {rules}', [
          'rules' => Yii::t('app', '7 players for each battle (excluded the battle uploader)'),
        ]),
      ),
      ['class' => 'mb-1'],
    ),
  ]) . "\n" ?>
</div>
<div class="mb-3">
  <p class="mb-1"><?=
    vsprintf('%s: %s', [
      Html::encode(Yii::t('app', 'Samples')),
      Html::encode($fmt->asInteger($totalSamples)),
    ])
  ?></p>
  <p class="mb-1"><?=
    vsprintf('%s %s', [
      Html::encode(Yii::t('app', 'Systematic error of win %') . ':'),
      Html::encode(
        Yii::t('app', '{pct_point} percentage point', [
          'pct_point' => $fmt->asDecimal(
            $error,
            2,
            [],
            [
              NumberFormatter::POSITIVE_PREFIX => '+',
              NumberFormatter::NEGATIVE_PREFIX => '-',
            ]
          ),
        ]),
      ),
    ])
  ?></p>
</div>
