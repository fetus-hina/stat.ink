<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\AboutAsset;
use app\components\helpers\TypeHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Language;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 */

$title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app-translate', 'About Translation'),
]);

TypeHelper::instanceOf($this->context, Controller::class)->layout = 'main';
$this->title = $title;

AboutAsset::register($this);

$languages = ArrayHelper::sort(
  Language::find()->standard()->cache(86400)->all(),
  fn (Language $a, Language $b): int => strcmp($a->name, $b->name),
);

?>
<div class="container">
  <h1>
    <?= Html::encode(Yii::t('app-translate', 'About Translation')) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <h2>
    <?= Html::encode(Yii::t('app-translate', 'Supported Languages')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(Yii::t('app-translate', '{siteName} is available in the following languages:', [
      'siteName' => Yii::$app->name,
    ])) . "\n" ?>
  </p>
  <ul>
<?php foreach ($languages as $lang) { ?>
    <?= Html::tag(
      'li',
      implode(' ', array_filter([
        Html::tag('span', Html::encode($lang->name), ['lang' => $lang->lang]),
        $lang->name !== $lang->name_en
          ? Html::tag('span', Html::encode(sprintf('(%s)', $lang->name_en)), ['lang' => 'en-US'])
          : null,
      ])),
    ) . "\n" ?>
<?php } ?>
  </ul>

  <h2>
    <?= Html::encode(Yii::t('app-translate', 'How We Translate')) . "\n" ?>
  </h2>
  <ul>
<?php $list = [
  Yii::t('app-translate', 'The original texts are written in Japanese and English by the developer.'),
  Yii::t('app-translate', 'The other languages are translated using AI (large language models), so they may contain mistranslations or unnatural expressions.'),
  Yii::t('app-translate', 'For in-game terms such as weapons, stages, and gear, we use the official translations wherever possible.'),
  Yii::t('app-translate', 'Texts that have not been translated yet are displayed in English.'),
] ?>
<?php foreach ($list as $text) { ?>
    <?= Html::tag('li', Html::encode($text)) . "\n" ?>
<?php } ?>
  </ul>

  <h2>
    <?= Html::encode(Yii::t('app-translate', 'Help Us Improve')) . "\n" ?>
  </h2>
  <p>
    <?= Html::encode(Yii::t('app-translate', 'If you find a mistranslation or an unnatural expression, please let us know on GitHub. Pull requests to the translation files are also welcome.')) . "\n" ?>
  </p>
  <ul>
    <li>
      <?= Html::a(
        implode(' ', [
          Icon::github(),
          Html::encode(Yii::t('app-translate', 'Report a translation issue')),
        ]),
        'https://github.com/fetus-hina/stat.ink/issues',
      ) . "\n" ?>
    </li>
    <li>
      <?= Html::a(
        implode(' ', [
          Icon::refresh(),
          Html::encode(Yii::t('app-translate', 'How to update the translation files')),
        ]),
        'https://github.com/fetus-hina/stat.ink/wiki/Translation',
      ) . "\n" ?>
    </li>
  </ul>
</div>
