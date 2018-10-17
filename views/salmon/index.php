<?php
declare(strict_types=1);

use app\assets\RpgAwesomeAsset;
use app\components\grid\SalmonActionColumn;
use app\components\widgets\AdWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\Label;
use app\components\widgets\SalmonUserInfo;
use app\components\widgets\SnsWidget;
use app\models\Salmon2;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\i18n\Formatter;
use yii\widgets\ListView;

$title = Yii::t('app-salmon2', "{name}'s Salmon Log", ['name' => $user->name]);
$this->title = sprintf('%s | %s', Yii::$app->name, $title);

// $this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
// $this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
// $this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
// $this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
// $this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
// $this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
// $this->registerMetaTag([
//   'name' => 'twitter:image',
//   'content' => $user->iconUrl,
// ]);
// if ($user->twitter != '') {
//   $this->registerMetaTag(['name' => 'twitter:creator', 'content' => sprintf('@%s', $user->twitter)]);
// }
?>
<div class="container">
  <span itemscope itemtype="http://schema.org/BreadcrumbList">
    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <?= Html::tag('meta', '', ['itemprop' => 'url', 'content' => Url::home(true)]) . "\n" ?>
      <?= Html::tag('meta', '', ['itemprop' => 'title', 'content' => Yii::$app->name]) . "\n" ?>
    </span>
  </span>
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <?= SnsWidget::widget([
  ]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
      <p>
<?php RpgAwesomeAsset::register($this); ?>
        <?= Html::a(
          '<span class="ra ra-crossed-swords"></span> ' . Yii::t('app', 'Battles'),
          ['show-v2/user', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-default']
        ) . "\n" ?>
      </p>
      <div class="text-right">
        <?= ListView::widget([
          'dataProvider' => $dataProvider,
          'itemOptions' => [ 'tag' => false ],
          'layout' => '{pager}',
          'pager' => [
            'maxButtonCount' => 5
          ],
        ]) . "\n" ?>
      </div>
      <?= GridView::widget([
        'options' => [
          'id' => 'battles',
          'class' => 'table-responsive',
        ],
        'layout' => '{items}',
        'dataProvider' => $dataProvider,
        'formatter' => [
          'class' => Formatter::class,
          'nullDisplay' => '',
        ],
        'tableOptions' => ['class' => 'table table-striped table-condensed'],
        'rowOptions' => function (Salmon2 $model): array {
          return [
            'class' => [
              'battle-row',
            ],
            'data' => [
              'period' => $model->shift_period,
            ],
          ];
        },
        'columns' => [
          [
            'class' => SalmonActionColumn::class,
            'user' => $user,
          ],
          [
            'headerOptions' => ['class' => 'cell-splatnet'],
            'contentOptions' => ['class' => 'cell-splatnet'],
            'attribute' => 'splatnet_number',
            'label' => '#',
            'format' => 'integer',
          ],
          [
            'attribute' => 'stage_id',
            'headerOptions' => ['class' => 'cell-map'],
            'contentOptions' => ['class' => 'cell-map'],
            'label' => Yii::t('app-app', 'Stage'),
            'value' => function (Salmon2 $model): ?string {
              return $model->stage_id
                ? Yii::t('app-salmon-map2', $model->stage->name)
                : null;
            },
          ],
          [
            'label' => Yii::t('app', 'Result'),
            'headerOptions' => ['class' => 'cell-result'],
            'contentOptions' => ['class' => 'cell-result'],
            'format' => 'raw',
            'value' => function (Salmon2 $model): ?string {
              $isCleared = $model->getIsCleared();
              if ($isCleared === null) {
                return null;
              } elseif ($isCleared) {
                return Label::widget([
                  'color' => 'success',
                  'content' => Yii::t('app-salmon2', 'Cleared'),
                ]);
              } else {
                return implode(' ', [
                  Label::widget([
                    'color' => 'danger',
                    'content' => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                      'waveNumber' => Yii::$app->formatter->asInteger($model->clear_waves + 1),
                    ]),
                  ]),
                  $model->fail_reason_id
                    ? Label::widget([
                      'color' => 'warning',
                      'content' => Yii::t('app-salmon2', $model->failReason->name),
                    ])
                    : '',
                ]);
              }
            },
          ],
          [
            'label' => Yii::t('app', 'Title'),
            'headerOptions' => ['class' => 'cell-title'],
            'contentOptions' => ['class' => 'cell-title'],
            'value' => function (Salmon2 $model): ?string {
              if (!$model->title_before_id) {
                return null;
              }

              return implode(' ', [
                Yii::t('app-salmon-title2', $model->titleBefore->name),
                $model->title_before_exp === null
                  ? ''
                  : Yii::$app->formatter->asInteger($model->title_before_exp),
              ]);
            },
          ],
          [
            'label' => Yii::t('app', 'Title (After)'),
            'headerOptions' => ['class' => 'cell-title-after'],
            'contentOptions' => ['class' => 'cell-title-after'],
            'value' => function (Salmon2 $model): ?string {
              if (!$model->title_after_id) {
                return null;
              }

              return implode(' ', [
                Yii::t('app-salmon-title2', $model->titleAfter->name),
                $model->title_after_exp === null
                  ? ''
                  : Yii::$app->formatter->asInteger($model->title_after_exp),
              ]);
            },
          ],
          [
            'label' => Yii::t('app', 'Date Time'),
            'headerOptions' => ['class' => 'cell-datetime'],
            'contentOptions' => ['class' => 'cell-datetime'],
            'format' => 'raw',
            'value' => function (Salmon2 $model): ?string {
              return $model->start_at === null
                ? null
                : Html::tag(
                  'time',
                  Html::encode(Yii::$app->formatter->asDateTime($model->start_at, 'short')),
                  ['datetime' => Yii::$app->formatter->asDateTime($model->start_at, 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ')]
                );
            },
          ],
          [
            // reltime {{{
            'label' => Yii::t('app', 'Relative Time'),
            'headerOptions' => ['class' => 'cell-reltime'],
            'contentOptions' => ['class' => 'cell-reltime'],
            'format' => 'raw',
            'value' => function (Salmon2 $model): ?string {
              return $model->start_at === null
                ? null
                : Html::tag(
                  'time',
                  Html::encode(Yii::$app->formatter->asRelativeTime($model->start_at)),
                  [ 
                    'datetime' => Yii::$app->formatter->asDateTime($model->start_at, 'yyyy-MM-dd\'T\'HH:mm:ssZZZZZ'),
                    'class' => 'auto-tooltip',
                    'title' => Yii::$app->formatter->asDateTime($model->start_at),
                  ]
                );
            },
            // }}}
          ],
        ],
      ]) . "\n" ?>
      <div class="text-right">
        <?= ListView::widget([
          'dataProvider' => $dataProvider,
          'itemOptions' => [ 'tag' => false ],
          'layout' => '{pager}',
          'pager' => [
            'maxButtonCount' => 5
          ]
        ]) . "\n" ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= SalmonUserInfo::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12" id="table-config">
      <div>
        <label>
          <input type="checkbox" id="table-hscroll" value="1">
          <?= Html::encode(Yii::t('app', 'Always enable horizontal scroll')) . "\n" ?>
        <label>
      </div>
      <div class="row"><?php
        $_list = [
          'cell-splatnet' => Yii::t('app', 'SplatNet #'),
          'cell-map' => Yii::t('app', 'Stage'),
          'cell-result' => Yii::t('app', 'Result'),
          'cell-title' => Yii::t('app', 'Title'),
          'cell-title-after' => Yii::t('app', 'Title (After)'),
          'cell-datetime' => Yii::t('app', 'Date Time'),
          'cell-reltime' => Yii::t('app', 'Relative Time'),
        ];
        foreach ($_list as $k => $v) {
          echo Html::tag(
            'div',
            Html::tag(
              'label',
              sprintf(
                '%s %s',
                Html::tag('input', '', ['type' => 'checkbox', 'class' => 'table-config-chk', 'data-klass' => $k]),
                Html::encode($v)
              )
            ),
            ['class' => 'col-xs-6 col-sm-4 col-lg-3']
          );
        }
      ?></div>
    </div>
  </div>
</div>
<?php
//FIXME
$this->registerJs('window.battleList();window.battleListConfig();');
?>
