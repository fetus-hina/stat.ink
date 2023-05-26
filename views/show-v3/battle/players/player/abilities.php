<?php

declare(strict_types=1);

use app\assets\Spl3AbilityAsset;
use app\components\helpers\SendouInk;
use app\components\widgets\Icon;
use app\models\Ability3;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use app\models\GearConfigurationSecondary3;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BattlePlayer3|BattleTricolorPlayer3 $player
 * @var View $this
 * @var array<string, Ability3> $abilities
 */

$gears = [
  $player->headgear,
  $player->clothing,
  $player->shoes,
];

if (count(array_filter($gears, fn ($gear) => (bool)$gear)) < 3) {
  return;
}

$className = 'abilities-' . substr(hash('sha256', __FILE__), 0, 8);

$powers = ArrayHelper::map(
  $abilities,
  'key',
  fn (Ability3 $model): array => [
    'key' => $model->key,
    'name' => Yii::t('app-ability3', $model->name),
    'rank' => $model->rank,
    'mainOnly' => $model->primary_only,
    'main' => $model->primary_only ? false : 0,
    'sub' => $model->primary_only ? null : 0,
    'ap' => $model->primary_only ? false : 0,
  ],
);
foreach ($gears as $gear) {
  $mainKey = ArrayHelper::getValue($gear, 'ability.key');
  if ($mainKey && isset($powers[$mainKey])) {
    if ($powers[$mainKey]['mainOnly']) {
      $powers[$mainKey]['main'] = true;
      $powers[$mainKey]['ap'] = true;
    } else {
      $powers[$mainKey]['main']++;
      $powers[$mainKey]['ap'] += 10;
    }
  }

  $doubler = $mainKey === 'ability_doubler';
  foreach ($gear->gearConfigurationSecondary3s as $sub) {
    $subKey = ArrayHelper::getValue($sub, 'ability.key');
    if (
      $subKey &&
      isset($powers[$subKey]) &&
      !$powers[$subKey]['mainOnly']
    ) {
      $powers[$subKey]['sub'] += $doubler ? 2 : 1;
      $powers[$subKey]['ap'] += $doubler ? 6 : 3;
    }
  }
}

$powers = array_values(
  array_filter(
    $powers,
    fn (array $info): bool => $info['ap'] === true || $info['ap'] > 0,
  ),
);

usort(
  $powers,
  fn (array $a, array $b): int => (($a['mainOnly'] ? 1 : 0) <=> ($b['mainOnly'] ? 1 : 0))
    ?: (int)$b['ap'] <=> (int)$a['ap']
    ?: (int)$b['main'] <=> (int)$a['main']
    ?: (int)$b['sub'] <=> (int)$b['sub']
    ?: $a['rank'] <=> $b['rank'],
);

if ($powers) {
  BootstrapAsset::register($this);
  BootstrapPluginAsset::register($this);

  $css = [
    '.ability-table' => [
      'table-layout' => 'fixed',
    ],
    '.ability-col-icon' => [
      'width' => 'calc(1.5em + 8px * 2)',
    ],
    '.ability-col-main, .ability-col-sub, .ability-col-ap' => [
      'width' => '5em',
    ],
    '.ability-table tbody .ability-col-icon' => [
      'background' => '#333',
    ],
  ];

  $this->registerCss(
    implode('', array_map(
      fn (string $selector, array $style): string => vsprintf('%s{%s}', [
        $selector,
        Html::cssStyleFromArray($style),
      ]),
      array_keys($css),
      array_values($css),
    )),
  );
}

$modalId = vsprintf('%s-%s', [
  $className,
  substr(hash('sha1', uniqid('', true)), 0, 8),
]);

$sendouInkUrl = SendouInk::getBuildUrl3($player->weapon, ...$gears);

?>
<?= Html::beginTag('div', ['class' => ['mt-1', $className]]) . "\n" ?>
<?php if ($powers) { ?>
  <div class="text-right">
    <?= Html::button(
      Icon::popup() . ' ' . Html::encode(Yii::t('app', 'Ability')),
      [
        'class' => 'btn btn-default btn-sm',
        'data' => [
          'toggle' => 'modal',
          'target' => sprintf('#%s', $modalId),
        ],
        'style' => [
          'cursor' => 'pointer',
        ],
      ],
    ) . "\n" ?>
  </div>
  <?= Html::beginTag('div', [
    'class' => 'fade modal',
    'id' => $modalId,
    'tabindex' => '-1',
  ]) . "\n" ?>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <?= Html::button(
            Icon::close(),
            ['class' => 'close', 'data-dismiss' => 'modal'],
          ) . "\n" ?>
          <?= Html::tag(
            'h4',
            Html::encode(Yii::t('app', 'Ability')),
            ['class' => 'modal-title'],
          ) . "\n" ?>
        </div>
        <div class="modal-body p-0 mt-2 mb-0">
          <table class="table table-striped ability-table mb-0">
            <thead>
              <tr>
                <th class="omit text-center ability-col-name"></th>
                <?= Html::tag(
                  'th',
                  Html::encode(Yii::t('app', 'Effects')),
                  [
                    'class' => ['ability-col-ap', 'auto-tooltip', 'omit', 'text-center'],
                    'title' => Yii::t('app', 'Effects'),
                  ],
                ) . "\n" ?>
                <?= Html::tag(
                  'th',
                  Html::encode(Yii::t('app', 'Mains')),
                  [
                    'class' => ['ability-col-main', 'auto-tooltip', 'omit', 'text-center'],
                    'title' => Yii::t('app', 'Primary Ability'),
                  ],
                ) . "\n" ?>
                <?= Html::tag(
                  'th',
                  Html::encode(Yii::t('app', 'Subs')),
                  [
                    'class' => ['ability-col-sub', 'auto-tooltip', 'omit', 'text-center'],
                    'title' => Yii::t('app', 'Secondary Abilities'),
                  ],
                ) . "\n" ?>
              </tr>
            </thead>
            <tbody>
<?php foreach ($powers as $power) { ?>
              <tr>
                <td class="omit"><?= Html::encode($power['name']) ?></td>
<?php if ($power['mainOnly']) { ?>
                <td class="text-center text-success"><?= Icon::check() ?></td>
                <td></td>
                <td></td>
<?php } else {?>
                <?= Html::tag(
                  'td',
                  Html::encode(Yii::$app->formatter->asDecimal($power['ap'] / 10, 1)),
                  ['class' => 'fw-bold text-center'],
                ) . "\n" ?>
                <?= Html::tag(
                  'td',
                  $power['main'] > 0
                    ? Html::encode(Yii::$app->formatter->asInteger($power['main']))
                    : '',
                  ['class' => 'text-center'],
                ) . "\n" ?>
                <?= Html::tag(
                  'td',
                  $power['sub'] > 0
                    ? Html::encode(Yii::$app->formatter->asInteger($power['sub']))
                    : '',
                  ['class' => 'text-center'],
                ) . "\n" ?>
<?php } ?>
              </tr>
<?php } ?>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <?= ($sendouInkUrl
            ? Html::a(
              implode(' ', [
                Icon::popup(),
                Html::encode(Yii::t('app', 'Check with Setup Analyzer')),
              ]),
              $sendouInkUrl,
              [
                'class' => 'btn btn-default',
                'rel' => 'nofollow noreferrer',
                'target' => '_blank',
              ],
            )
            : ''
          ) . "\n" ?>
          <?= Html::button(
            implode(' ', [
              Icon::close(),
              Html::encode(Yii::t('app', 'Close')),
            ]),
            [
              'class' => 'btn btn-default',
              'data-dismiss' => 'modal',
            ],
          ) . "\n" ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
</div>
