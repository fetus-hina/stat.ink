<?php

declare(strict_types=1);

use app\assets\BlackoutHintAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$_mode = $mode ?? 'ambiguous';
$_id = $id ?? 'blackout_list';

BlackoutHintAsset::register($this);

?>
<div class="table-responsive">
  <?= Html::beginTag('table', ['id' => $_id, 'class' => 'blackout-info table table-bordered table-condensed']) . "\n" ?>
    <thead>
      <tr>
        <th><?= Html::encode(Yii::t('app-rule', 'Solo Queue')) ?></th>
        <th>
<?php if ($_mode === 'splatoon2'): ?>
          <?= Html::encode(Yii::t('app-rule2', 'League Battle (Twin)')) . "\n" ?>
<?php elseif ($_mode === 'splatoon1'): ?>
          <?= Html::encode(Yii::t('app-rule', 'Squad Battle (Twin)')) . "\n" ?>
<?php else: ?>
          <?= Html::encode(Yii::t('app-rule2', 'League Battle (Twin)')) ?><br>
          <?= Html::encode(Yii::t('app-rule', 'Squad Battle (Twin)')) . "\n" ?>
<?php endif ?>
        </th>
        <th>
<?php if ($_mode === 'splatoon2'): ?>
          <?= Html::encode(Yii::t('app-rule2', 'League Battle (Quad)')) . "\n" ?>
<?php elseif ($_mode === 'splatoon1'): ?>
          <?= Html::encode(Yii::t('app-rule', 'Squad Battle (Tri)')) ?><br>
          <?= Html::encode(Yii::t('app-rule', 'Squad Battle (Quad)')) . "\n" ?>
<?php else: ?>
          <?= Html::encode(Yii::t('app-rule2', 'League Battle (Quad)')) ?><br>
          <?= Html::encode(Yii::t('app-rule', 'Squad Battle (Tri)')) ?><br>
          <?= Html::encode(Yii::t('app-rule', 'Squad Battle (Quad)')) . "\n" ?>
<?php endif ?>
        </th>
        <th><?= Html::encode(Yii::t('app-rule', 'Private Battle')) ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
<?php $_categories = [
  'user' => Yii::t('app', 'You'),
  'good-guys' => Yii::t('app', 'Good Guys'),
  'bad-guys' => Yii::t('app', 'Bad Guys'),
] ?>
<?php foreach (['standard', 'squad_2', 'squad_4', 'private'] as $_mode): ?>
        <td>
          <?= implode(
            '<br>',
            array_map(
              function ($category, $name) use ($_mode) : string {
                return implode('', [
                  Html::tag('span', '', [
                    'class' => 'blackout-info-icon far fa-fw fa-square',
                    'data' => [
                      'mode' => $_mode,
                      'category' => $category,
                    ],
                  ]),
                  Html::encode($name),
                ]);
              },
              array_keys($_categories),
              array_values($_categories)
            )
          ) . "\n" ?>
        </td>
<?php endforeach ?>
      </tr>
    </tbody>
  </table>
  <p class="blackout-info-legends">
    <?= Html::encode(Yii::t('app', 'Legends')) ?>:
    <span class="far fa-fw fa-square"></span><?= Html::encode(Yii::t('app', 'No black out')) . "\n" ?>
    /
    <span class="far fa-fw fa-check-square"></span><?= Html::encode(Yii::t('app', 'Black out')) . "\n" ?>
  </p>
</div>
