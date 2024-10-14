<?php

declare(strict_types=1);

use app\assets\ColorSchemeAsset;
use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Ability3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatAbility3XUsage;
use app\models\Weapon3;
use jp3cki\yii2\jqueryColor\JqueryColorAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Ability3[] $abilities
 * @var Rule3 $rule
 * @var Season3 $season
 * @var StatWeapon3XUsageRange $xRange
 * @var StatWeapon3XUsageRange[] $xRanges
 * @var View $this
 * @var Weapon3[] $weapons
 * @var array<int, Rule3> $rules
 * @var array<int, Season3> $seasons
 * @var array<int, StatAbility3XUsage> $data
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 * @var callable(StatWeapon3XUsageRange): string $xRangeUrl
 */

$title = Yii::t('app', 'Average Gear Abilities');
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $title);

ColorSchemeAsset::register($this);
JqueryColorAsset::register($this);

$this->registerCss('.vmiddle{vertical-align:middle!important}');
$maxValue = 0.0;

?>
<div class="container">
  <?= Html::tag(
    'h1',
    implode(' ', [
      Icon::s3AbilityInkSaverMain(),
      Html::encode($title),
    ]),
  ) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <div class="mb-1">
      <?= $this->render(
        'includes/season-selector',
        compact('season', 'seasonUrl', 'seasons'),
      ) . "\n" ?>
    </div>
    <?= $this->render('weapons3/rule-tabs', compact('rule', 'rules', 'ruleUrl')) . "\n" ?>
    <?= $this->render('weapons3/x-range-tabs', [
      'disableAll' => true,
      'xRange' => $xRange,
      'xRangeUrl' => $xRangeUrl,
      'xRanges' => $xRanges,
    ]) . "\n" ?>
  </div>

  <div class="mb-3">
    <p class="mb-1">
      <?= Yii::t('app', 'Aggregated: {rules}', [
        'rules' => implode(', ', [
          Icon::s3LobbyX() . ' ' . Html::encode(Yii::t('app-lobby3', 'X Battle')),
          Html::encode(Yii::t('app', '7 players for each battle (excluded the battle uploader)')),
        ]),
      ]) . "\n" ?>
    </p>
    <p class="mb-1">
      <?= Html::encode(
        Yii::t('app', 'Primary ability is counted as {value_1_0} and secondary is counted as {value_0_3}.', [
          'value_1_0' => Yii::$app->formatter->asDecimal(1.0, 1),
          'value_0_3' => Yii::$app->formatter->asDecimal(0.3, 1),
        ]),
      ) . "\n" ?>
      <?= Html::encode(Yii::t('app', 'The abilities valid only for the primary means the rate of mounting.')) . "\n" ?>
    </p>
  </div>

  <div class="table-responsive table-responsive-force mb-3">
    <table class="table table-striped table-bordered table-condensed small">
      <thead><?= $this->render('./ability3/header', compact('abilities')) ?></thead>
      <tfoot><?= $this->render('./ability3/header', compact('abilities')) ?></tfoot>
      <tbody>
<?php $lastType = null ?>
<?php foreach ($weapons as $weapon) { ?>
<?php
    if ($lastType !== $weapon->mainweapon->type_id) {
      if ($lastType !== null) {
        echo $this->render('./ability3/header', compact('abilities')) . "\n";
      }

      $lastType = $weapon->mainweapon->type_id;
    }
?>
<?php $rowData = $data[$weapon->id] ?? [] ?>
        <?= Html::beginTag('tr', ['data-key' => $weapon->key]) . "\n" ?>
          <th scope="row" class="text-nowrap vmiddle">
            <?= Icon::s3Weapon($weapon, size: '1.2em') . "\n" ?>
            <?= Icon::s3Subweapon($weapon->subweapon, size: '0.8em') . "\n" ?>
            <?= Icon::s3Special($weapon->special, size: '0.8em') . "\n" ?>
          </th>
          <td class="text-right small vmiddle text-muted">
            <?= Yii::$app->formatter->asInteger($rowData['players'] ?? 0) . "\n" ?>
          </td>
<?php foreach ($abilities as $ability) { ?>
<?php $value = ArrayHelper::getValue($rowData, "{$ability->key}_avg", 0.0) / 10.0 ?>
<?php $maxValue = max($maxValue, $value) ?>
          <?= Html::tag(
            'td',
            Yii::$app->formatter->asDecimal($value, 2),
            [
              'class' => 'text-center small vmiddle ability-data',
              'data' => [
                'value' => (string)$value,
              ],
            ],
          ) . "\n" ?>
<?php } ?>
          <th scope="row" class="text-nowrap vmiddle">
            <?= Icon::s3Weapon($weapon, size: '1.2em') . "\n" ?>
          </th>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
<?php
if ($maxValue > 0.0) {
  $this->registerJs(<<<JS
    $('.ability-data').each(function () {
      const \$this = $(this);
      const bgColor = jQuery
        .Color({
          hue: 214,
          saturation: 0.57,
          lightness: 0.47,
        })
        .alpha(parseFloat(\$this.data('value')) / $maxValue)
        .blend(jQuery.Color('#ffffff'));

      const y = Math.round(bgColor.red() * 0.299 + bgColor.green() * 0.587 + bgColor.blue() * 0.114);
      \$this.css({
        'background-color': bgColor.toRgbaString(),
        'color': y > 153 ? '#000' : '#fff',
      });
    });
  JS);
}
?>
