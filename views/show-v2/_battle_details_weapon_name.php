<?php

declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\components\widgets\AbilityIcon;
use app\models\Battle2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle2 $battle
 * @var View $this
 */

if (!$battle || !$battle->weapon) {
  return;
}

$icons = Spl2WeaponAsset::register($this);
$rows = [];
$rows[] = implode(' ', [
  implode('', [
    Html::img($icons->getIconUrl($battle->weapon->key), [
      'style' => [
        'height' => '1.333em',
      ],
    ]),
    Html::encode(Yii::t('app-weapon2', $battle->weapon->name)),
  ]),
  Html::tag(
    'small',
    vsprintf('(%s)', [
      implode(', ', [
        implode('', [
          Html::img($icons->getIconUrl('sub/' . $battle->weapon->subweapon->key), [
            'style' => [
              'height' => '1.333em',
            ],
          ]),
          Html::encode(Yii::t('app-subweapon2', $battle->weapon->subweapon->name)),
        ]),
        implode('', [
          Html::img($icons->getIconUrl('sp/' . $battle->weapon->special->key), [
            'style' => [
              'height' => '1.333em',
            ],
          ]),
          Html::encode(Yii::t('app-special2', $battle->weapon->special->name)),
        ]),
      ]),
    ]),
    ['style' => [
      'font-size' => '75%',
    ]]
  ),
]);

if (
  ($abilities = $battle->getGearAbilitySummary()) &&
  isset($abilities['main_power_up']) &&
  $abilities['main_power_up']->get57Format() > 0
) {
  $rows[] = Html::tag(
    'div',
    implode('', [
      AbilityIcon::spl2('main_power_up', ['style' => [
        'height' => '1.333em',
      ]]),
      Html::encode(Yii::t('app-ability2', $battle->weapon->mainPowerUp->name)),
    ]),
    ['style' => [
      'margin-left' => '2.5em',
    ]]
  );
}

// if (
//   $battle->weapon &&
//   $battle->version &&
//   ($attack = $battle->weapon->getWeaponAttack($battle->version))
// ) {
//   $rows[] = Html::tag(
//     'div',
//     Html::encode(implode(', ', array_filter([
//         $attack->damage,
//         $attack->damage2,
//         $attack->damage3,
//     ]))),
//     ['style' => [
//       'margin-left' => '2.5em',
//     ]]
//   );
// }

echo Html::tag(
  'div',
  implode('', array_map(
    function (string $html): string {
      return Html::tag('div', $html);
    },
    $rows
  ))
);
