<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Salmon3;
use app\models\SalmonScheduleWeapon3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app-salmon2', 'Rotation'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->is_private) {
      return Html::encode(
        Yii::t('app-salmon3', 'Private Job'),
      );
    }

    $schedule = $model->schedule;
    if (!$schedule) {
      return null;
    }

    $weapons = SalmonScheduleWeapon3::find()
      ->with(['weapon', 'random'])
      ->andWhere(['schedule_id' => $schedule->id])
      ->orderBy(['id' => SORT_ASC])
      ->all();
    if (!$weapons) {
      return null;
    }

    $gameModeIconHtml = '';
    if ($schedule->big_map_id) {
      $asset = GameModeIconsAsset::register(Yii::$app->view);
      $gameModeIconHtml = Html::img(
        Yii::$app->assetManager->getAssetUrl($asset, 'spl3/salmon-bigrun.png'),
        [
          'title' => Yii::t('app-salmon3', 'Big Run'),
          'class' => 'auto-tooltip basic-icon',
        ],
      );
      $gameModeIconHtml .= Html::encode(' ');
    }

    $weaponsHtml = implode('', array_map(
      function (SalmonScheduleWeapon3 $info): string {
        if ($info->weapon || $info->random) {
          Yii::$app->view->registerCss(vsprintf('.schedule-weapon-icon{%s}', [
            Html::cssStyleFromArray([
              'background' => '#333',
              'border-radius' => '50%',
              'display' => 'inline-block',
              'margin' => '0 0.333em 0 0',
              'padding' => '0.25em',
            ]),
          ]));
        }

        return Html::tag(
          'span',
          WeaponIcon::widget([
            'model' => $info->weapon ?? $info->random,
          ]),
          ['class' => 'schedule-weapon-icon'],
        );
      },
      $weapons,
    ));

    return $gameModeIconHtml . $weaponsHtml;
  },
];
