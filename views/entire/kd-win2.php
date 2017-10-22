<?php
use app\actions\entire\KDWin2Action;
use app\assets\AppOptAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Map2;
use app\models\RankGroup2;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use yii\bootstrap\ActiveForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$title = Yii::t('app', 'Winning Percentage based on K/D');
$this->title = Yii::$app->name . ' | ' . $title;

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$optAsset = AppOptAsset::register($this);
$optAsset->registerJsFile($this, 'kd-win.js');

$this->registerCss(implode('', [
  '.kdcell{width:' . (100 / (KDWin2Action::KD_LIMIT + 2)) . '%!important}',
  '.percent-cell{font-size:61.8%}',
  '.center{text-align:center!important}',
]));
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>
  <p>
    <?= Html::encode(Yii::t('app', 'This website has implemented support for color-blindness. Please check "Color-Blind Support" in the "User Name/Guest" menu of the navbar to enable it.')) . "\n" ?>
  </p>
  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <ul class="nav nav-tabs">
    <li class="active"><a href="javascript:;">Splatoon 2</a></li>
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
    <?= $_form->field($filter, 'map')->label(false)->dropDownList(array_merge(
      ['' => Yii::t('app-map2', 'Any Stage')],
      Map2::getSortedMap()
    )) . "\n" ?>
    <?= $_form->field($filter, 'rank')->label(false)->dropDownList(array_merge(
      ['' => Yii::t('app-rank2', 'Any Rank')],
      ArrayHelper::map(
        RankGroup2::find()->orderBy(['id' => SORT_DESC])->asArray()->all(),
        function (array $row) : string {
          return '~' . $row['key'];
        },
        function (array $row) : string {
          return Yii::t('app-rank2', $row['name']);
        }
      )
    )) . "\n" ?>
    <?= $_form->field($filter, 'weapon')->label(false)->dropDownList(array_merge(
      ['' => Yii::t('app-weapon2', 'Any Weapon')],
      (function () {
        // {{{
        $ret = [];
        $q = WeaponCategory2::find()
          ->orderBy(['id' => SORT_ASC])
          ->with([
            'weaponTypes' => function (ActiveQuery $query) : void {
              $query->orderBy([
                'category_id' => SORT_ASC,
                'rank' => SORT_ASC,
                'id' => SORT_ASC,
              ]);
            },
            'weaponTypes.weapons',
          ]);
        foreach ($q->all() as $category) {
          $categoryName = Yii::t('app-weapon2', $category->name);
          foreach ($category->weaponTypes as $type) {
            $typeName = Yii::t('app-weapon2', $type->name);
            $groupLabel = ($categoryName !== $typeName)
              ? sprintf('%s » %s', $categoryName, $typeName)
              : $typeName;
            $weapons = ArrayHelper::map(
              $type->weapons,
              'key',
              function (Weapon2 $weapon) : string {
                return Yii::t('app-weapon2', $weapon->name);
              }
            );
            if ($weapons) {
              uasort($weapons, 'strnatcasecmp');
              $ret[$groupLabel] = (count($weapons) > 1)
                ? array_merge(
                  ['@' . $type->key => Yii::t('app-weapon2', 'All of {0}', $typeName)],
                  $weapons
                )
                : $weapons;
            }
          }
        }
        return $ret;
        // }}}
      })()
    )) . "\n" ?>
    <?= $_form->field($filter, 'term')->label(false)->dropDownList(array_merge(
      ['' => Yii::t('app-version2', 'Any Version')],
      (function () {
        $list = ArrayHelper::map(
          SplatoonVersion2::find()->asArray()->all(),
          function (array $row) : string {
            return 'v' . $row['tag'];
          },
          function (array $row) : string {
            return Yii::t('app-version2', $row['name']);
          }
        );
        uksort($list, function (string $a, string $b) : int {
          return version_compare($b, $a);
        });
        return $list;
      })()
    )) . "\n" ?>
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

  <h3>
    <?= Html::encode(Yii::t('app', 'Legend')) . "\n" ?>
  </h3>
  <div class="table-responsive" style="max-width:8em;margin-right:2em;float:left">
    <table class="table table-bordered table-condensed rule-table">
      <tbody>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="90">
          <td class="text-center kdcell">90%</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="<?= (10+(90-10)*5/6) ?>">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="<?= (10+(90-10)*4/6) ?>">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="<?= (10+(90-10)*3/6) ?>">
          <td class="text-center kdcell">50%</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="<?= (10+(90-10)*2/6) ?>">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="<?= (10+(90-10)*1/6) ?>">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="1" data-percent="10">
          <td class="text-center kdcell">10%</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="table-responsive" style="max-width:8em;margin-right:2em;float:left">
    <table class="table table-bordered table-condensed rule-table">
      <tbody>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="100" data-percent="100">
          <td class="text-center kdcell"><?= Html::encode(Yii::t('app', 'Many')) ?></td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="42" data-percent="100">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="33" data-percent="100">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="25" data-percent="100">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="17" data-percent="100">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="8" data-percent="100">
          <td class="text-center kdcell">:</td>
        </tr>
        <tr>
          <td class="text-center kdcell percent-cell" data-battle="0" data-percent="100">
          <td class="text-center kdcell"><?= Html::encode(Yii::t('app', 'Few')) ?></td>
        </tr>
      </tbody>
    </table>
  </div>
  <div style="clear:left"></div>

<?php
$_q = Rule2::find()->orderBy(['id' => SORT_ASC]);
if ($filter->map === 'mystery') {
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
    <table class="table table-bordered table-condensed rule-table">
      <thead>
        <tr>
          <?= Html::tag(
            'th',
            implode('', [
              Html::encode(Yii::t('app', 'd')),
              '＼',
              Html::encode(Yii::t('app', 'k')),
            ]),
            ['class' => 'text-center kdcell']
          ) . "\n" ?>
<?php foreach (range(0, KDWin2Action::KD_LIMIT) as $v) { ?>
          <?= Html::tag(
            'th',
            Html::encode(($v === KDWin2Action::KD_LIMIT)
              ? $v . '+'
              : $v),
            ['class' => 'text-center kdcell']
          ) . "\n" ?>
<?php } ?>
        </tr>
      </thead>
      <tbody>
<?php foreach (range(0, KDWin2Action::KD_LIMIT) as $d) { ?>
        <tr>
          <?= Html::tag(
            'th',
            Html::encode(
              ($d === KDWin2Action::KD_LIMIT)
                ? $d . '+'
                : $d
            ),
            ['class' => 'text-center kdcell']
          ) . "\n" ?>
<?php foreach (range(0, KDWin2Action::KD_LIMIT) as $k) { ?>
          <?= (function () use ($k, $d, $data, $rule) {
            $v = $data[$rule->key][$k][$d] ?? null;
            $rate = ($v && ($v['battles'] ?? 0) > 0)
              ? $v['wins'] / $v['battles']
              : null;
            return Html::tag(
              'td',
              implode('<br>', [
                Html::encode(sprintf(
                  '%d / %d',
                  ($v && $v['wins'] ?? 0) ? $v['wins'] : 0,
                  ($v && $v['battles'] ?? 0) ? $v['battles'] : 0
                )),
                $rate === null
                  ? '-'
                  : Html::encode(Yii::$app->formatter->asPercent($rate, 1))
              ]),
              [
                'class' => 'text-center kdcell percent-cell',
                'data' => [
                  'battle' => ($v && $v['battles'] ?? 0) ? $v['battles'] : 0,
                  'percent' => sprintf('%f', $rate * 100),
                ],
              ]
            );
          })() . "\n" ?>
<?php } ?>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
<?php } ?>
</div>
