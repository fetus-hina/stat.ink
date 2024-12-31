<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\entire\KDWin2Action;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\kdWin\KDWinTable;
use app\components\widgets\kdWin\LegendWidget;
use app\models\Map2;
use app\models\RankGroup2;
use app\models\Rule2;
use app\models\SplatoonVersionGroup2;
use app\models\WeaponType2;
use yii\bootstrap\ActiveForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$title = Yii::t('app', 'Winning Percentage based on K/D');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

TableResponsiveForceAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <p>
    <?= Html::encode(Yii::t(
      'app',
      'This website has color-blind support. Please check "Color-Blind Support" in the "Username/Guest" menu of the navbar to enable it.'
    )) . "\n" ?>
  </p>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <ul class="nav nav-tabs" aria-role="navigation">
    <li><?= Html::a('Splatoon 3', ['entire/kd-win3']) ?></li>
    <li class="active"><a>Splatoon 2</a></li>
    <li><?= Html::a('Splatoon', ['entire/kd-win']) ?></li>
  </ul>

<?php // filter {{{ ?>
<?php $this->registerCss('.help-block{display:none}') ?>
  <?php $_form = ActiveForm::begin([
    'id' => 'filter-form',
    'action' => ['entire/kd-win2'],
    'method' => 'get',
    'options' => [
      'class' => 'form-inline',
      'style' => [
        'margin-top' => '15px',
      ],
    ],
    'enableClientValidation' => false,
  ]); echo "\n" ?>
    <?= $_form->field($filter, 'map')
      ->label(false)
      ->dropDownList(array_merge(
        ['' => Yii::t('app-map2', 'Any Stage')],
        Map2::getSortedMap()
      )) . "\n"
    ?>
    <?= $_form->field($filter, 'rank')
      ->label(false)
      ->dropDownList(array_merge(
        ['' => Yii::t('app-rank2', 'Any Rank')],
        ArrayHelper::map(
          RankGroup2::find()
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all(),
          'key',
          function (array $row): string {
            return Yii::t('app-rank2', $row['name']);
          }
        )
      )) . "\n"
    ?>
    <?= $_form->field($filter, 'weapon')
      ->label(false)
      ->dropDownList(array_merge(
        ['' => Yii::t('app-weapon2', 'Any Weapon')],
        ArrayHelper::map(
          WeaponType2::find()
            ->orderBy([
              'category_id' => SORT_ASC,
              'rank' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
          'key',
          function (array $row): string {
            return Yii::t('app-weapon2', $row['name']);
          }
        )
      )) . "\n"
    ?>
<?php $versions = SplatoonVersionGroup2::find()->asArray()->all() ?>
<?php usort($versions, function (array $a, array $b): int {
  return version_compare($b['tag'], $a['tag']);
}) ?>
    <?= $_form->field($filter, 'version')
      ->label(false)
      ->dropDownList(array_merge(
        ['*' => Yii::t('app-version2', 'Any Version')],
        ArrayHelper::map(
          $versions,
          'tag',
          function (array $row): string {
            return Yii::t('app', 'Version {0}', [
              Yii::t('app-version2', $row['name']),
            ]);
          }
        )
      )) . "\n"
    ?>
    <?= Html::tag(
      'div',
      Html::submitButton(
        Html::encode(Yii::t('app', 'Summarize')),
        ['class' => 'btn btn-primary']
      ),
      ['class' => 'form-group']
    ) . "\n" ?>
  <?php ActiveForm::end(); echo "\n" ?>
<?php // }}} ?>

  <?= LegendWidget::widget() . "\n" ?>

<?php
$_q = Rule2::find()->orderBy(['id' => SORT_ASC]);
if (substr((string)$filter->map, 0, 7) === 'mystery') {
  $_q->andWhere(['key' => 'nawabari']);
}
if ($filter->rank) {
  $_q->andWhere(['<>', 'key', 'nawabari']);
}
?>
<?php foreach ($_q->all() as $rule) { ?>
  <?= Html::tag(
    'h2',
    Html::encode(Yii::t('app-rule2', $rule->name)),
    ['id' => $rule->key]
  ) . "\n" ?>
  <div class="table-responsive table-responsive-force">
    <?= KDWinTable::widget([
      'data' => $data[$rule->key] ?? [],
      'limit' => KDWin2Action::KD_LIMIT,
    ]) . "\n" ?>
  </div>
<?php } ?>
</div>
