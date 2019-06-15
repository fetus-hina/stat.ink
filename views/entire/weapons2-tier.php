<?php
declare(strict_types=1);

use app\assets\Spl2WeaponAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
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

$weaponIcons = Spl2WeaponAsset::register($this);

?>
<div class="container">
  <h1><?= Html::encode(vsprintf('%s (%s, %s) - %s', [
    Yii::t('app-rule2', $rule->name),
    $month,
    Yii::t('app', 'Version {0}', [
      Yii::t('app-version2', $versionGroup->name),
    ]),
    Yii::t('app', 'Weapon Tier'),
  ])) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p class="m-0">野良ガチマのランクXのみ集計。投稿者は除外済</p>
  <table class="table">
    <thead>
      <tr>
        <th style="width:calc(3em + 16px)"></th>
        <th>Win %</th>
        <th style="width:calc(8em + 16px)">K</th>
        <th style="width:calc(8em + 16px)">D</th>
        <th style="width:calc(4em + 16px)">KR</th>
        <th style="width:calc(2em + 16px)">n</th>
    </thead>
    <tbody>
<?php foreach ($data as $model) { ?>
      <tr>
        <td><?= Html::img($weaponIcons->getIconUrl($model->weapon->key), [
          'title' => Yii::t('app-weapon2', $model->weapon->name),
          'class' => 'auto-tooltip',
          'style' => [
            'width' => '3em',
            'height' => 'auto',
          ],
        ]) ?></td>
        <td><?php
          if ($_rate = $model->getWinRates()) {
            if ($_rate[0] === null) {
              echo Html::tag(
                'div',
                implode('', [
                  Html::tag(
                    'div',
                    Yii::$app->formatter->asPercent($_rate[1], 2),
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
            } else {
              echo Html::tag(
                'div',
                implode('', [
                  Html::tag(
                    'div',
                    Html::tag(
                      'span',
                      vsprintf('%s±%s%%', [
                        Yii::$app->formatter->asDecimal($_rate[1] * 100, 2),
                        Yii::$app->formatter->asDecimal(($_rate[2] - $_rate[0]) * 100 / 2, 2),
                      ]),
                      ['class' => 'pl-2']
                    ),
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
            }
          }
        ?></td>
        <td>
          <?= vsprintf('%s±%s', [
            Yii::$app->formatter->asDecimal($model->avg_kill, 2),
            Yii::$app->formatter->asDecimal($model->stderr_kill * 2, 2),
          ]) ?><br>
          median=<?= Yii::$app->formatter->asDecimal($model->med_kill, 2) ?>
        </td>
        <td>
          <?= vsprintf('%s±%s', [
            Yii::$app->formatter->asDecimal($model->avg_death, 2),
            Yii::$app->formatter->asDecimal($model->stderr_death * 2, 2),
          ]) ?><br>
          median=<?= Yii::$app->formatter->asDecimal($model->med_death, 2) ?>
        </td>
        <td>
          <?= $model->avg_death > 0
            ? Yii::$app->formatter->asDecimal($model->avg_kill / $model->avg_death, 3)
            : '-'
          ?>
        </td>
        <td><?= Yii::$app->formatter->asInteger($model->players_count) ?></td>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
