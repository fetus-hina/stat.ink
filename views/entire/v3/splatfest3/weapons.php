<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Splatfest3StatsWeapon;
use app\models\Splatfest3;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Splatfest3 $splatfest
 * @var Splatfest3StatsWeapon[] $weaponsChallenge
 * @var Splatfest3StatsWeapon[] $weaponsOpen
 * @var View $this
 */

if (!$weaponsChallenge && !$weaponsOpen) {
  return;
}

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <?= Html::encode(Yii::t('app', 'Weapon Stats')) . "\n" ?>
  </div>
  <div class="panel-body pb-0">
    <div class="mb-3">
      <p class="mb-1">
        <?= Html::encode(
          Yii::t('app', 'Aggregated: {rules}', [
            'rules' => Yii::t('app', '7 players for each battle (excluded the battle uploader)'),
          ]),
        ) . "\n" ?>
      </p>
    </div>
    <?= Tabs::widget([
      'items' => array_values(
        array_filter(
          [
            [
              'active' => true,
              'label' => implode(' - ', [
                Yii::t('app-lobby3', 'Splatfest (Pro)'),
                Yii::t('app', 'Detailed'),
              ]),
              'content' => $weaponsChallenge
                ? $this->render('weapons/table', [
                  'splatfest' => $splatfest,
                  'models' => $weaponsChallenge,
                ])
                : null,
            ],
            [
              'label' => implode(' - ', [
                Yii::t('app-lobby3', 'Splatfest (Pro)'),
                Yii::t('app', 'Win %'),
              ]),
              'content' => $weaponsChallenge
                ? $this->render('weapons/win-rate', ['models' => $weaponsChallenge])
                : null,
            ],
            [
              'active' => !$weaponsChallenge,
              'label' => implode(' - ', [
                Yii::t('app-lobby3', 'Splatfest (Open)'),
                Yii::t('app', 'Detailed'),
              ]),
              'content' => $weaponsOpen
                ? $this->render('weapons/table', [
                  'splatfest' => $splatfest,
                  'models' => $weaponsOpen,
                ])
                : null,
            ],
            [
              'label' => implode(' - ', [
                Yii::t('app-lobby3', 'Splatfest (Open)'),
                Yii::t('app', 'Win %'),
              ]),
              'content' => $weaponsOpen
                ? $this->render('weapons/win-rate', ['models' => $weaponsOpen])
                : null,
            ],
          ],
          fn (array $item): bool => $item['content'] !== null,
        ),
      ),
      'tabContentOptions' => [
        'class' => 'mt-3 tab-content'
      ],
    ]) . "\n" ?>
  </div>
</div>
