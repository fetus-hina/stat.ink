<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\ColorSchemeAsset;
use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Dialog;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Ability3;
use app\models\Rule3;
use app\models\Season3;
use app\models\Special3;
use app\models\StatAbility3XUsage;
use app\models\StatWeapon3XUsageRange;
use app\models\Subweapon3;
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
 * @var array<int, list<array{error: float, config: array<string, array{primary: string, secondaries: list<string>}>, sendouInkUrl: string|null}>> $typicalGears
 * @var callable(Rule3): string $ruleUrl
 * @var callable(Season3): string $seasonUrl
 * @var callable(StatWeapon3XUsageRange): string $xRangeUrl
 */

$title = Yii::t('app-entire', 'Average Gear Abilities');
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $title);

ColorSchemeAsset::register($this);
JqueryColorAsset::register($this);

$this->registerCss(implode('', [
  '.vmiddle{vertical-align:middle!important}',
  '.typical-gear-trigger{cursor:pointer}',
  '.table.typical-gear-premise>tbody>tr>td,.table.typical-gear-premise>tbody>tr>th{border-top:0}',
  '.typical-gear-premise th{white-space:nowrap;width:1%}',
  '.typical-gear-candidate .gear-block{background:#333;border-radius:5px;line-height:1;padding:0 2px}',
  '.typical-gear-candidate .main-ability{font-size:3em;vertical-align:baseline;width:auto}',
  '.typical-gear-candidate .sub-ability{font-size:1.75em;vertical-align:baseline;width:auto}',
  '@media(max-width:767px){',
  '.typical-gear-candidate .main-ability{font-size:2em}',
  '.typical-gear-candidate .sub-ability{font-size:1.25em}',
  '}',
]));
$maxValue = 0.0;
$typicalGearDialogId = 'typical-gear-dialog';

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
      <?= Yii::t('app-entire', 'Aggregated: {rules}', [
        'rules' => implode(', ', [
          Icon::s3LobbyX() . ' ' . Html::encode(Yii::t('app-lobby3', 'X Battle')),
          Html::encode(Yii::t('app-entire', '7 players for each battle (excluded the battle uploader)')),
        ]),
      ]) . "\n" ?>
    </p>
    <p class="mb-1">
      <?= Html::encode(
        Yii::t('app-entire', 'Primary ability is counted as {value_1_0} and secondary is counted as {value_0_3}.', [
          'value_1_0' => Yii::$app->formatter->asDecimal(1.0, 1),
          'value_0_3' => Yii::$app->formatter->asDecimal(0.3, 1),
        ]),
      ) . "\n" ?>
      <?= Html::encode(Yii::t('app-entire', 'The abilities valid only for the primary means the rate of mounting.')) . "\n" ?>
    </p>
    <p class="mb-1">
      <?= Html::encode(
        Yii::t('app-entire', 'Click or tap the weapon icons at the left end of each row to show example gear setups.'),
      ) . "\n" ?>
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
<?php $candidates = $typicalGears[$weapon->id] ?? [] ?>
        <?= Html::beginTag('tr', ['data-key' => $weapon->key]) . "\n" ?>
          <?= Html::beginTag('th', [
            'class' => array_filter([
              'text-nowrap',
              'vmiddle',
              $candidates ? 'typical-gear-trigger' : null,
            ]),
            'data' => $candidates
              ? [
                'candidates' => $candidates,
                'icon' => Icon::s3Weapon($weapon),
                'special' => $weapon->special?->key,
                'sub' => $weapon->subweapon?->key,
                'weapon' => Yii::t('app-weapon3', $weapon->name),
                'title' => Yii::t('app-entire', 'Example Gear Setups for {weapon}', [
                  'weapon' => Yii::t('app-weapon3', $weapon->name),
                ]),
              ]
              : [],
            'role' => $candidates ? 'button' : null,
            'scope' => 'row',
            'tabindex' => $candidates ? '0' : null,
          ]) . "\n" ?>
            <?= Icon::s3Weapon($weapon, size: '1.2em') . "\n" ?>
            <?= Icon::s3Subweapon($weapon->subweapon, size: '0.8em') . "\n" ?>
            <?= Icon::s3Special($weapon->special, size: '0.8em') . "\n" ?>
          <?= Html::endTag('th') . "\n" ?>
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
<?= Dialog::widget([
  'body' => implode('', [
    Html::tag(
      'table',
      Html::tag('tbody', implode('', [
        Html::tag('tr', implode('', [
          Html::tag('th', Html::encode(Yii::t('app', 'Mode')), ['scope' => 'row']),
          Html::tag('td', implode(' ', [
            Icon::s3Rule($rule) ?? '',
            Html::encode(Yii::t('app-rule3', $rule->name)),
          ])),
        ])),
        Html::tag('tr', implode('', [
          Html::tag('th', Html::encode(Yii::t('app', 'Weapon')), ['scope' => 'row']),
          Html::tag('td', '', ['class' => 'typical-gear-premise-weapon']),
        ])),
        Html::tag('tr', implode('', [
          Html::tag('th', Html::encode(Yii::t('app', 'Sub Weapon')), ['scope' => 'row']),
          Html::tag('td', '', ['class' => 'typical-gear-premise-sub']),
        ])),
        Html::tag('tr', implode('', [
          Html::tag('th', Html::encode(Yii::t('app', 'Special')), ['scope' => 'row']),
          Html::tag('td', '', ['class' => 'typical-gear-premise-special']),
        ])),
      ])),
      ['class' => 'table table-condensed typical-gear-premise mb-0'],
    ),
    Html::tag('div', '', ['class' => 'typical-gear-candidates']),
  ]),
  'footer' => Dialog::FOOTER_CLOSE,
  'id' => $typicalGearDialogId,
  'title' => '',
]) . "\n" ?>
<?php
$maxCandidates = max([0, ...array_map(count(...), $typicalGears)]);
$this->registerJs(vsprintf('(%s)(%s);', [
  <<<'JS'
    function (options) {
      const $dialog = $(options.dialog);
      const $container = $('.typical-gear-candidates', $dialog);
      const icon = function (key) {
        return $('<span class="typical-gear-ability">')
          .attr('title', options.abilityNames[key] || '')
          .append($.parseHTML(options.abilityIcons[key] || ''));
      };
      const destroyTooltips = function () {
        $('.typical-gear-ability', $container).tooltip('destroy');
      };

      const renderGear = function (gear) {
        return $('<span class="d-inline-block gear-block">').append(
          $('<span class="main-ability">').append(icon(gear.primary)),
          $('<span class="sub-ability">').append(gear.secondaries.map(icon))
        );
      };

      const renderCandidate = function (candidate, index) {
        const $header = $('<div class="d-flex justify-content-between align-items-center mb-1">')
          .append($('<h5 class="m-0">').text(options.labels[index] || ''));
        if (candidate.sendouInkUrl) {
          $header.append(
            $('<a class="btn btn-default btn-xs">')
              .attr({
                href: candidate.sendouInkUrl,
                rel: 'nofollow noreferrer',
                target: '_blank'
              })
              .append($.parseHTML(options.analyzerLabel))
          );
        }

        const config = candidate.config;
        return $('<div class="typical-gear-candidate">')
          .append(
            $header,
            $('<div>').append(
              renderGear(config.headgear),
              ' ',
              renderGear(config.clothing),
              ' ',
              renderGear(config.shoes)
            )
          );
      };

      const fillPremise = function (selector, iconHtml, name) {
        $(selector, $dialog)
          .empty()
          .append(
            $.parseHTML(iconHtml || ''),
            document.createTextNode(' ' + (name || '-'))
          );
      };

      const open = function ($trigger) {
        const sub = options.subweapons[$trigger.data('sub')] || {};
        const special = options.specials[$trigger.data('special')] || {};
        fillPremise('.typical-gear-premise-weapon', $trigger.attr('data-icon'), String($trigger.data('weapon')));
        fillPremise('.typical-gear-premise-sub', sub.icon, sub.name);
        fillPremise('.typical-gear-premise-special', special.icon, special.name);

        $('.modal-title', $dialog)
          .empty()
          .append(
            $.parseHTML(String($trigger.attr('data-icon') || '')),
            document.createTextNode(' ' + String($trigger.data('title')))
          );
        destroyTooltips();
        $container
          .empty()
          .append(
            $trigger.data('candidates').flatMap(function (candidate, index) {
              return [$('<hr>'), renderCandidate(candidate, index)];
            })
          );
        $('.typical-gear-ability', $container).tooltip({ container: 'body' });
        $dialog.modal('show');
      };

      // Tooltips are attached to the body, so they may remain after the dialog is closed
      $dialog.on('hide.bs.modal', function () {
        $('.typical-gear-ability', $container).tooltip('hide');
      });

      $(document)
        .on('click', '.typical-gear-trigger', function () {
          open($(this));
        })
        .on('keydown', '.typical-gear-trigger', function (ev) {
          if (ev.key === 'Enter' || ev.key === ' ') {
            ev.preventDefault();
            open($(this));
          }
        });
    }
  JS,
  json_encode(
    [
      'abilityIcons' => array_combine(
        array_map(fn (Ability3 $ability): string => $ability->key, $abilities),
        array_map(fn (Ability3 $ability): string => Icon::s3Ability($ability) ?? '', $abilities),
      ),
      'abilityNames' => array_combine(
        array_map(fn (Ability3 $ability): string => $ability->key, $abilities),
        array_map(fn (Ability3 $ability): string => Yii::t('app-ability3', $ability->name), $abilities),
      ),
      'analyzerLabel' => implode(' ', [
        Icon::popup(),
        Html::encode(Yii::t('app-show-v3', 'Check with Setup Analyzer')),
      ]),
      'dialog' => '#' . $typicalGearDialogId,
      'specials' => (object)ArrayHelper::map(
        array_filter(array_map(fn (Weapon3 $weapon): ?Special3 => $weapon->special, $weapons)),
        'key',
        fn (Special3 $special): array => [
          'icon' => Icon::s3Special($special) ?? '',
          'name' => Yii::t('app-special3', $special->name),
        ],
      ),
      'subweapons' => (object)ArrayHelper::map(
        array_filter(array_map(fn (Weapon3 $weapon): ?Subweapon3 => $weapon->subweapon, $weapons)),
        'key',
        fn (Subweapon3 $sub): array => [
          'icon' => Icon::s3Subweapon($sub) ?? '',
          'name' => Yii::t('app-subweapon3', $sub->name),
        ],
      ),
      'labels' => array_map(
        fn (int $n): string => Yii::t('app-entire', 'Example Gear Setup {number}', ['number' => $n]),
        $maxCandidates > 0 ? range(1, $maxCandidates) : [],
      ),
    ],
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
  ),
]));
?>
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
