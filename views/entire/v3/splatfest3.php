<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Splatfest3;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Splatfest3 $splatfest
 * @var Splatfest3[] $festList
 * @var View $this
 * @var array<string, int> $votes
 * @var array<string, string> $colors
 * @var array<string, string> $names
 */

$title = Yii::t('app', 'Estimated Vote %');
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $this->title);

$this->registerCss(
    implode(
        '',
        array_map(
            fn (string $key): string => vsprintf('.progress-bar-%s{background-color:#%s}', [
                $key,
                $colors[$key],
            ]),
            ['team1', 'team2', 'team3'],
        ),
    ),
);

?>
<div class="container">
  <?= Html::tag(
    'h1',
    Html::encode(Yii::t('app', 'Estimated Vote %')),
  ) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <?= Html::dropDownList(
        '',
        Url::to(['entire/splatfest3', 'id' => $splatfest->id], true),
        ArrayHelper::map(
            $festList,
            fn (Splatfest3 $fest): string => Url::to(['entire/splatfest3', 'id' => $fest->id], true),
            'name',
        ),
        [
            'class' => 'form-control mb-0',
            'onchange' => 'window.location.href = this.value',
        ],
    ) . "\n" ?>
  </div>

  <p class="mb-1 small text-muted">
    <?= Html::encode(
      vsprintf('%s: %s', [
        Yii::t('app', 'Samples'),
        Yii::$app->formatter->asInteger(array_sum($votes)),
      ]),
    ) . "\n" ?>
  </p>
  <p class="mb-1 small text-muted">
    <?= Yii::t('app', 'Idea: {source}', [
      'source' => Html::a(
        vsprintf('%s %s', [
          Icon::twitter(),
          '@splatoon_weapon',
        ]),
        'https://twitter.com/splatoon_weapon/status/1612147667446157313',
      ),
    ]) . "\n" ?>
  </p>

  <div class="row">
    <div class="col-xs-12 mb-3" style="max-width:400px">
      <?= Progress::widget([
          'bars' => array_map(
              fn (string $key, int $count): array => [
                  'percent' => 100.0 * $count / array_sum($votes),
                  'label' => Yii::$app->formatter->asPercent($count / array_sum($votes), 0),
                  'options' => [
                      'class' => "progress-bar-{$key} auto-tooltip",
                      'title' => $names[$key],
                  ],
              ],
              array_keys($votes),
              array_values($votes),
          ),
      ]) . "\n" ?>
    </div>
    <div class="col-xs-12 mb-3">
      <table class="table table-striped" style="width:auto">
        <thead>
          <tr>
            <th></th>
            <th><?= Html::encode(Yii::t('app', 'Team')) ?></th>
            <th><?= Html::encode(Yii::t('app', 'Vote %')) ?></th>
            <th><?= Html::encode(Yii::t('app', 'Samples')) ?></th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($votes as $key => $count) { ?>
          <tr>
            <?= Html::tag('td', '', [
              'style' => [
                'width' => '1ex',
                'background-color' => "#{$colors[$key]}",
              ],
            ]) . "\n" ?>
            <td><?= Html::encode($names[$key]) ?></td>
            <?= Html::tag(
              'td',
              Html::encode(Yii::$app->formatter->asPercent($count / array_sum($votes), 1)),
              ['class' => 'text-right'],
            ) . "\n" ?>
            <?= Html::tag(
              'td',
              Html::encode(Yii::$app->formatter->asInteger($count)),
              ['class' => 'text-right'],
            ) . "\n" ?>
          </tr>
<?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
