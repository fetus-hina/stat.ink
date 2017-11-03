<?php
use app\assets\SortableTableAsset;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\GearType;
use app\models\Language;
use yii\helpers\Html;

$title = Yii::t('app', 'API Info: Gears: {0}', [
    Yii::t('app-gear', $type->name),
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

SortableTableAsset::register($this);
?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p>
    <?= implode(' | ', array_map(
        function (GearType $_type) use ($type) : string {
          return ($_type->id == $type->id)
            ? Html::encode(Yii::t('app-gear', $_type->name))
            : Html::a(
              Html::encode(Yii::t('app-gear', $_type->name)),
              ['api-info/gear2-' . $_type->key]
            );
        },
        GearType::find()->orderBy(['id' => SORT_ASC])->all()
    )) . "\n" ?>
  </p>
  <div class="table-responsive table-responsive-force">
    <table class="table table-striped table-condensed table-sortable">
      <thead>
        <tr>
          <?= Html::tag(
            'th',
            Html::encode(Yii::t('app', 'Brand')),
            ['data-sort' => 'string']
          ) . "\n" ?>
          <?= Html::tag(
            'th',
            Html::encode(Yii::t('app', 'Primary Ability')),
            ['data-sort' => 'string']
          ) . "\n" ?>
          <?= Html::tag(
            'th',
            Html::tag('code', Html::encode('key')),
            ['data-sort' => 'string']
          ) . "\n" ?>
          <?= Html::tag(
            'th',
            Html::encode(Yii::t('app', 'SplatNet')),
            ['data-sort' => 'int']
          ) . "\n" ?>
<?php foreach ($langs as $lang) { ?>
          <?= Html::tag(
            'th',
            Html::encode($lang->name),
            ['data-sort' => 'string']
          ) . "\n" ?>
<?php } ?>
        </tr>
      </thead>
      <tbody>
<?php foreach ($gears as $_gear) { ?>
        <?= Html::tag(
          'tr',
          implode("\n", array_merge(
            [
              Html::tag(
                'td',
                Html::encode(Yii::t('app-brand2', $_gear->brand->name)),
                ['data-sort-value' => Yii::t('app-brand2', $_gear->brand->name)]
              ),
              Html::tag(
                'td',
                Html::encode(Yii::t('app-ability2', $_gear->ability->name ?? '')),
                ['data-sort-value' => Yii::t('app-ability2', $_gear->ability->name ?? '')]
              ),
              Html::tag(
                'td',
                Html::tag('code', Html::encode($_gear->key)),
                ['data-sort-value' => $_gear->key]
              ),
              Html::tag(
                'td',
                $_gear->splatnet === null ? '' : Html::tag('code', Html::encode($_gear->splatnet)),
                ['data-sort-value' => $_gear->splatnet ?? -1]
              ),
            ],
            array_map(
              function (Language $lang) use ($_gear) : string {
                $text = Yii::$app->i18n->translate('app-gear2', $_gear->name, [], $lang->lang);
                return Html::tag(
                  'td',
                  Html::encode($text),
                  ['data-sort-value' => $text]
                );
              },
              $langs
            )
          ))
        ) . "\n" ?>
        </tr>
<?php } ?>
      </tbody>
    </table>
  </div>
  <hr>
  <p>
    <?= Html::img('/static-assets/cc/cc-by.svg', ['alt' => 'CC-BY 4.0']) ?><br>
    <?= Yii::t('app', 'This document is under a <a href="http://creativecommons.org/licenses/by/4.0/deed.en">Creative Commons Attribution 4.0 International License</a>.') . "\n" ?>
  </p>
</div>
