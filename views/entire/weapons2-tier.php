<?php
declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\FA;
use app\components\widgets\SnsWidget;
use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

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
if ($prev) {
  $this->registerLinkTag(['rel' => 'prev', 'href' => $prev]);
}
if ($next) {
  $this->registerLinkTag(['rel' => 'next', 'href' => $next]);
}

$weaponIcons = Spl2WeaponAsset::register($this);
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

  <nav>
    <div class="row mb-3">
      <div class="col-xs-6 text-left"><?php
        if ($prev) {
          echo Html::a(
            implode(' ', [
              (string)FA::fas('angle-double-left')->fw(),
              Html::encode(Yii::t('app', 'Prev.')),
            ]),
            $prev,
            ['class' => 'btn btn-default']
          );
        }
      ?></div>
      <div class="col-xs-6 text-right"><?php
        if ($next) {
          echo Html::a(
            implode(' ', [
              Html::encode(Yii::t('app', 'Next')),
              (string)FA::fas('angle-double-right')->fw(),
            ]),
            $next,
            ['class' => 'btn btn-default']
          );
        }
      ?></div>
    </div>
  </nav>

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
          Yii::$app->formatter->asInteger(50),
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

  <nav class="mb-3"><?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => array_map(
        function (string $key, array $data) use ($versionGroup, $month, $rule): array {
            return [
                'label' => Yii::t('app-rule2', $data['name']),
                'url' => ['entire/weapons2-tier',
                    'version' => $versionGroup->tag,
                    'month' => $month,
                    'rule' => $key,
                ],
                'active' => $key === $rule->key,
                'options' => [
                    'class' => array_filter([
                        $data['enabled'] ? null : 'disabled',
                    ]),
                ],
            ];
        },
        array_keys($rules),
        array_values($rules),
    ),
  ]) ?></nav>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th style="width:calc(3em + 16px)"></th>
          <th style="width:56px"></th>
          <th style="min-width:300px"><?= Html::encode(Yii::t('app', 'Win %')) ?></th>
          <th style="width:calc(8em + 16px)"><?= Html::encode(Yii::t('app', 'Kills')) ?></th>
          <th style="width:calc(8em + 16px)"><?= Html::encode(Yii::t('app', 'Deaths')) ?></th>
          <th style="width:calc(4em + 16px)"><?= Html::encode(Yii::t('app', 'Ratio')) ?></th>
          <th style="width:calc(2em + 16px)">n</th>
      </thead>
      <tbody>
<?php foreach ($data as $model) { ?>
<?php $_rate = $model->getWinRates() ?>
        <tr>
          <td class="text-center align-middle"><?php
            if ($_rate && $_rate[0] !== null) {
              if ($_rate[0] > 0.5) {
                echo FA::far('smile')->size('2x')->fw();
              } elseif ($_rate[2] < 0.5) {
                echo FA::far('frown')->size('2x')->fw();
              }
            }
          ?></td>
          <td class="text-center align-middle"><?= implode('', [
            Html::img($weaponIcons->getIconUrl($model->weapon->key), [
              'title' => Yii::t('app-weapon2', $model->weapon->name),
              'class' => 'auto-tooltip',
              'style' => [
                'width' => '40px',
                'height' => 'auto',
              ],
            ]),
            '<br>',
            Html::img($weaponIcons->getIconUrl('sub/' . $model->weapon->subweapon->key), [
              'title' => Yii::t('app-subweapon2', $model->weapon->subweapon->name),
              'class' => 'auto-tooltip',
              'style' => [
                'width' => '18px',
                'height' => 'auto',
              ],
            ]),
            Html::img($weaponIcons->getIconUrl('sp/' . $model->weapon->special->key), [
              'title' => Yii::t('app-special2', $model->weapon->special->name),
              'class' => 'auto-tooltip',
              'style' => [
                'width' => '18px',
                'height' => 'auto',
                'margin-left' => '4px',
              ],
            ]),
          ]) ?></td>
          <td class="align-middle"><?php
            if ($_rate) {
              if ($_rate[0] === null) {
                echo Html::tag(
                  'div',
                  implode('', [
                    Html::tag(
                      'div',
                      '',
                      [
                        'class' => 'progress-bar progress-bar-primary',
                        'style' => [
                          'width' => sprintf('%f%%', $_rate[1] * 100),
                        ],
                      ]
                    ),
                  ]),
                  ['class' => 'progress']
                );
                printf('%s±??.??%%', Yii::$app->formatter->asDecimal($_rate[1] * 100, 2));
              } else {
                echo Html::tag(
                  'div',
                  implode('', [
                    Html::tag(
                      'div',
                      '',
                      [
                        'class' => 'progress-bar progress-bar-primary text-left-important',
                        'style' => [
                          'width' => sprintf('%f%%', $_rate[0] * 100),
                        ],
                      ]
                    ),
                    Html::tag(
                      'div',
                      '',
                      [
                        'class' => 'progress-bar progress-bar-primary',
                        'style' => [
                          'width' => sprintf('%f%%', ($_rate[1] - $_rate[0]) * 100),
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
                          'width' => sprintf('%f%%', ($_rate[2] - $_rate[1]) * 100),
                          'opacity' => '0.3',
                        ],
                      ]
                    ),
                  ]),
                  ['class' => 'progress']
                );
                vprintf('%s±%s%%', [
                  Yii::$app->formatter->asDecimal($_rate[1] * 100, 2),
                  Yii::$app->formatter->asDecimal(($_rate[2] - $_rate[0]) * 100 / 2, 2),
                ]);
              }
            }
          ?></td>
          <td class="align-middle">
            <?= vsprintf('%s=%s±%s', [
              Html::tag('span', Html::encode('μ'), [
                'title' => Yii::t('app', 'Average'),
                'class' => 'auto-tooltip',
              ]),
              Yii::$app->formatter->asDecimal($model->avg_kill, 2),
              Yii::$app->formatter->asDecimal($model->stderr_kill * 2, 2),
            ]) ?><br>
            <?= vsprintf('%s=%s', [
              Html::tag('span', Html::encode('Med'), [
                'title' => Yii::t('app', 'Median'),
                'class' => 'auto-tooltip',
              ]),
              Yii::$app->formatter->asDecimal($model->med_kill, 1),
            ]) ?><br>
            <?= vsprintf('%s=%s', [
              Html::tag('span', Html::encode('σ'), [
                'title' => Yii::t('app', 'Standard Deviation'),
                'class' => 'auto-tooltip',
              ]),
              Yii::$app->formatter->asDecimal($model->stddev_kill, 3),
            ]) . "\n" ?>
          </td>
          <td class="align-middle">
            <?= vsprintf('%s=%s±%s', [
              Html::tag('span', Html::encode('μ'), [
                'title' => Yii::t('app', 'Average'),
                'class' => 'auto-tooltip',
              ]),
              Yii::$app->formatter->asDecimal($model->avg_death, 2),
              Yii::$app->formatter->asDecimal($model->stderr_death * 2, 2),
            ]) ?><br>
            <?= vsprintf('%s=%s', [
              Html::tag('span', Html::encode('Med'), [
                'title' => Yii::t('app', 'Median'),
                'class' => 'auto-tooltip',
              ]),
              Yii::$app->formatter->asDecimal($model->med_death, 1),
            ]) ?><br>
            <?= vsprintf('%s=%s', [
              Html::tag('span', Html::encode('σ'), [
                'title' => Yii::t('app', 'Standard Deviation'),
                'class' => 'auto-tooltip',
              ]),
              Yii::$app->formatter->asDecimal($model->stddev_death, 3),
            ]) . "\n" ?>
          </td>
          <td class="align-middle">
            <?= $model->avg_death > 0
              ? Yii::$app->formatter->asDecimal($model->avg_kill / $model->avg_death, 3)
              : '-'
            ?>
          </td>
          <td class="align-middle"><?= Yii::$app->formatter->asInteger($model->players_count) ?></td>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
</div>
