<?php

declare(strict_types=1);

use app\assets\Spl3AbilityAsset;
use app\models\BattlePlayer3;
use app\models\GearConfigurationSecondary3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BattlePlayer3 $player
 * @var View $this
 */

$gears = [
  $player->headgear,
  $player->clothing,
  $player->shoes,
];

if (count(array_filter($gears, fn ($gear) => (bool)$gear)) < 3) {
  return;
}

$am = Yii::$app->assetManager;
$asset = Spl3AbilityAsset::register($this);

$className = 'abilities-' . substr(hash('sha256', __FILE__), 0, 8);
$this->registerCss(implode('', [
  ".{$className}>span{background:#333;border-radius:5px;line-height:1;padding:0 2px}",
  ".{$className} .main-ability{height:1.5em;vertical-align:baseline;width:auto}",
  ".{$className} .sub-ability{height:1em;vertical-align:baseline;width:auto}",
]));

?>
<?= Html::beginTag('div', ['class' => ['mt-1', $className]]) . "\n" ?>
<?php foreach ($gears as $gear) { ?>
  <span class="d-inline-block"><?= implode('', [
    Html::img(
      $am->getAssetUrl(
        $asset,
        vsprintf('%s.png', [
            ArrayHelper::getValue($gear, 'ability.key', 'unknown'),
        ]),
      ),
      [
        'class' => 'auto-tooltip main-ability',
        'title' => Yii::t('app-ability3', ArrayHelper::getValue($gear, 'ability.name', '(Unknown)')),
      ],
    ),
    implode('', array_map(
      fn (?GearConfigurationSecondary3 $secondary): string => Html::img(
        $am->getAssetUrl(
          $asset,
          vsprintf('%s.png', [
            ArrayHelper::getValue($secondary, 'ability.key', 'unknown'),
          ]),
        ),
        [
          'class' => 'auto-tooltip sub-ability',
          'title' => Yii::t('app-ability3', ArrayHelper::getValue($secondary, 'ability.name', '(Unknown)')),
        ],
      ),
      array_slice(
        array_merge($gear->gearConfigurationSecondary3s, [null, null, null]),
        0,
        3,
      ),
    )),
  ]) ?></span>
<?php } ?>
</div>
