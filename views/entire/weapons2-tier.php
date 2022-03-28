<?php

declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\components\helpers\Html;
use app\components\widgets\AdWidget;
use app\components\widgets\FA;
use app\components\widgets\GameModeIcon;
use app\components\widgets\SnsWidget;
use app\models\Rule2;
use app\models\SplatoonVersionGroup2;
use app\models\StatWeapon2Tier;
use yii\bootstrap\Nav;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Rule2 $rule
 * @var SplatoonVersionGroup2 $versionGroup
 * @var StatWeapon2Tier[] $data
 * @var View $this
 * @var array<string, array{month: string, vTag: string, vName: string}> $versions
 * @var array<string, array{name: string, enabled: bool}>[] $rules
 * @var string $month
 */

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app', 'Weapon'),
  Yii::t('app', 'Version {0}', [
    Yii::t('app-version2', $versionGroup->name),
  ]),
  $month,
  Yii::t('app-rule2', $rule->name),
]);
$this->title = $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$kdCell = function (StatWeapon2Tier $model, string $column): string {
  return implode('<br>', [
    vsprintf('%s=%s±%s', [
      Html::tag('span', Html::encode('μ'), [
        'title' => Yii::t('app', 'Average'),
        'class' => 'auto-tooltip',
      ]),
      Yii::$app->formatter->asDecimal($model->{"avg_{$column}"}, 2),
      Yii::$app->formatter->asDecimal($model->{"stderr_{$column}"} * 2, 2),
    ]),
    vsprintf('%s=%s', [
      Html::tag('span', Html::encode('Med'), [
        'title' => Yii::t('app', 'Median'),
        'class' => 'auto-tooltip',
      ]),
      Yii::$app->formatter->asDecimal($model->{"med_{$column}"}, 1),
    ]),
    vsprintf('%s=%s', [
      Html::tag('span', Html::encode('σ'), [
        'title' => Yii::t('app', 'Standard Deviation'),
        'class' => 'auto-tooltip',
      ]),
      Yii::$app->formatter->asDecimal($model->{"stddev_{$column}"}, 3),
    ]),
  ]);
};
?>
<div class="container">
  <h1><?= Html::encode(vsprintf('%s (%s, %s) - %s (alpha)', [
    Yii::t('app-rule2', $rule->name),
    $month,
    Yii::t('app', 'Version {0}', [
      Yii::t('app-version2', $versionGroup->name),
    ]),
    Yii::t('app', 'Weapon Tier'),
  ])) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <ul class="mb-3">
    <li>
      Targets:
      <ul>
        <li>Ranked battles (not including League battles)</li>
        <li><?= Html::encode(vsprintf('Rank %s only', [
          version_compare($versionGroup->tag, '3.0', '>=') ? 'X' : 'S+',
        ])) ?></li>
        <li>Excluded the uploader (<?= Html::encode(Yii::$app->name) ?>'s user)</li>
        <li><?= Html::encode(vsprintf('Filtered: n%s%s', [
          (substr(Yii::$app->language, 0, 3) === 'ja-') ? '≧' : '≥',
          Yii::$app->formatter->asInteger(StatWeapon2Tier::PLAYERS_COUNT_THRESHOLD),
        ])) ?></li>
      </ul>
    </li>
    <li>
      Kills and deaths:
      <ul>
        <li>Normalized to 5 minutes (even KO or overtimed)</li>
      </ul>
    </li>
    <li>
      ±:
      <ul>
        <li>
          Perhaps "the real value" is somewhere in the range.
          Don't too believe the representative (average) value.
        </li>
        <li>
          2&times;<i><abbr class="auto-tooltip" title="Standard Error">SE</abbr></i>
          (∼95% CI)
        </li>
      </ul>
    </li>
  </ul>

<?php if ($data) { ?>
  <p class="mb-3 text-right">
    Last Updated:
    <?= Yii::$app->formatter->asHtmlDatetime($data[0]->updated_at) ?>
    (<?= Yii::$app->formatter->asHtmlRelative($data[0]->updated_at) ?>)
  </p>
<?php } ?>

  <nav class="mb-3"><?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'encodeLabels' => false,
    'items' => array_map(
      fn (string $key, array $data): array => [
        'label' => implode(' ', [
          GameModeIcon::spl2($key),
          Html::encode(Yii::t('app-rule2', (string)$data['name'])), // @phpstan-ignore-line
        ]),
        'url' => ['entire/weapons2-tier',
          'version' => $versionGroup->tag,
          'month' => $month,
          'rule' => $key,
        ],
        'active' => $key === $rule->key,
        'options' => [
          'class' => array_filter(
            [
              $data['enabled'] === false ? 'disabled' : null, // @phpstan-ignore-line
            ],
            fn (?string $v): bool => $v !== null,
          ),
        ],
      ],
      array_keys($rules),
      array_values($rules),
    ),
  ]) ?></nav>

  <nav class="mb-2">
    <div class="form-group mb-0">
      <select id="versionChanger" class="form-control"><?= implode('', array_map(
        function (array $version) use ($month, $versionGroup, $rule): string {
          return Html::tag(
            'option',
            vsprintf('%s, %s', [
              substr($version['month'], 0, 7),
              Yii::t('app', 'Version {0}', [
                Yii::t('app-version2', $version['vName']),
              ]),
            ]),
            [
              'selected' => substr($version['month'], 0, 7) === substr($month, 0, 7) &&
                $version['vTag'] === $versionGroup->tag,
              'data' => [
                'url' => Url::to(['entire/weapons2-tier',
                  'version' => $version['vTag'],
                  'month' => substr($version['month'], 0, 7),
                  'rule' => $rule->key,
                ], true),
              ],
            ]
          );
        },
        $versions
      )) ?></select>
<?php $this->registerJs(sprintf(
  '$(%s).change(function(){location.href=$("option:selected", this).data("url")});',
  Json::encode('#versionChanger')
)) ?>
    </div>
  </nav>

  <div class="table-responsive"><?= GridView::widget([
    'dataProvider' => Yii::createObject([
      'class' => ArrayDataProvider::class,
      'allModels' => $data,
      'sort' => false,
      'pagination' => false,
    ]),
    'tableOptions' => ['class' => 'table'],
    'layout' => '{items}',
    'columns' => [
      [
        // smile icon {{{
        'label' => '',
        'contentOptions' => ['class' => 'text-center align-middle'],
        'headerOptions' => ['style' => ['width' => 'calc(3em + 16px)']],
        'format' => 'raw',
        'value' => function (StatWeapon2Tier $model): string {
          $rate = $model->getWinRates();
          if ($rate && $rate[0] !== null) {
            if ($rate[0] > 0.5) {
              return (string)FA::far('smile')->size('2x')->fw();
            } elseif ($rate[2] < 0.5) {
              return (string)FA::far('frown')->size('2x')->fw();
            }
          }
          return '';
        },
        // }}}
      ],
      [
        // Weapon {{{
        'label' => Html::tag('span', Html::encode(Yii::t('app', 'Weapon')), ['class' => 'sr-only']),
        'encodeLabel' => false,
        'contentOptions' => ['class' => 'text-center align-middle'],
        'headerOptions' => ['style' => ['width' => 'calc(40px + 16px)']],
        'format' => 'raw',
        'value' => function (StatWeapon2Tier $model): string {
          $weaponIcons = Spl2WeaponAsset::register($this);
          return vsprintf('<div>%s</div><div>%s%s</div>', [
            Html::img($weaponIcons->getIconUrl($model->weapon->key), [
              'title' => Yii::t('app-weapon2', $model->weapon->name),
              'class' => 'auto-tooltip',
              'style' => [
                'width' => '40px',
                'height' => '40px',
              ],
            ]),
            Html::img($weaponIcons->getIconUrl('sub/' . $model->weapon->subweapon->key), [
              'title' => Yii::t('app-subweapon2', $model->weapon->subweapon->name),
              'class' => 'auto-tooltip',
              'style' => [
                'width' => '18px',
                'height' => '18px',
              ],
            ]),
            Html::img($weaponIcons->getIconUrl('sp/' . $model->weapon->special->key), [
              'title' => Yii::t('app-special2', $model->weapon->special->name),
              'class' => 'auto-tooltip',
              'style' => [
                'width' => '18px',
                'height' => '18px',
                'margin-left' => '4px',
              ],
            ]),
          ]);
        },
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Win %'), // {{{
        'contentOptions' => ['class' => 'align-middle'],
        'headerOptions' => ['style' => ['min-width' => '300px']],
        'format' => 'raw',
        'value' => function (StatWeapon2Tier $model): ?string {
          if (!$rate = $model->getWinRates()) {
            return null;
          }

          if ($rate[0] === null) {
            // when cannot calc error {{{
            return implode('', [
              Html::tag(
                'div',
                implode('', [
                  Html::tag(
                    'div',
                    '',
                    [
                      'class' => 'progress-bar progress-bar-primary',
                      'style' => [
                        'width' => sprintf('%f%%', $rate[1] * 100),
                      ],
                    ]
                  ),
                ]),
                ['class' => 'progress']
              ),
              vsprintf('%s±??%s??%%', [
                Yii::$app->formatter->asDecimal($rate[1] * 100, 2),
                Yii::$app->formatter->decimalSeparator ?: '.',
              ]),
            ]);
            // }}}
          }

          return implode('', [
            Html::tag(
              'div',
              implode('', [
                Html::tag(
                  'div',
                  '',
                  [
                    'class' => 'progress-bar progress-bar-primary text-left-important',
                    'style' => [
                      'width' => sprintf('%f%%', $rate[0] * 100),
                    ],
                  ]
                ),
                Html::tag(
                  'div',
                  '',
                  [
                    'class' => 'progress-bar progress-bar-primary',
                    'style' => [
                      'width' => sprintf('%f%%', ($rate[1] - $rate[0]) * 100),
                      'opacity' => '0.65',
                    ],
                  ]
                ),
                Html::tag(
                  'div',
                  '',
                  [
                    'class' => 'progress-bar progress-bar-primary',
                    'style' => [
                      'width' => sprintf('%f%%', ($rate[2] - $rate[1]) * 100),
                      'opacity' => '0.3',
                    ],
                  ]
                ),
              ]),
              ['class' => 'progress']
            ),
            vsprintf('%s±%s%%', [
              Yii::$app->formatter->asDecimal($rate[1] * 100, 2),
              Yii::$app->formatter->asDecimal(($rate[2] - $rate[0]) * 100 / 2, 2),
            ]),
          ]);
        },
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Kills'), // {{{
        'contentOptions' => ['class' => 'align-middle'],
        'headerOptions' => ['style' => ['width' => 'calc(7em + 16px)']],
        'format' => 'raw',
        'value' => fn (StatWeapon2Tier $model): string => $kdCell($model, 'kill'),
        // }}}
      ],
      [
        'label' => Yii::t('app', 'Deaths'), // {{{
        'contentOptions' => ['class' => 'align-middle'],
        'headerOptions' => ['style' => ['width' => 'calc(7em + 16px)']],
        'format' => 'raw',
        'value' => fn (StatWeapon2Tier $model): string => $kdCell($model, 'death'),
        // }}}
      ],
      [
        'label' => Yii::t('app', 'KR'), // {{{
        'contentOptions' => ['class' => 'text-right align-middle'],
        'headerOptions' => [
          'class' => 'text-right',
          'style' => ['width' => 'calc(4em + 16px)'],
        ],
        'format' => ['decimal', 3],
        'value' => function (StatWeapon2Tier $model): ?float {
          return $model->avg_death > 0 ? ($model->avg_kill / $model->avg_death) : null;
        },
        // }}}
      ],
      [
        'label' => 'n', // {{{
        'contentOptions' => ['class' => 'text-right align-middle'],
        'headerOptions' => [
          'class' => 'text-right',
          'style' => ['width' => 'calc(4em + 16px)'],
        ],
        'format' => 'integer',
        'attribute' => 'players_count',
        // }}}
      ],
    ],
  ]) ?></div>
</div>
