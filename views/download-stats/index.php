<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use yii\helpers\Html;
use yii\web\View;

/** 
 * @var View $this
 */

$this->context->layout = 'main';
$title = Yii::t('app', 'Downloads');

$this->title = implode(' | ', [
  $title,
  Yii::$app->name,
]);

OgpHelper::default($this, title: $this->title);

?>
<div class="container">
  <h1>
    <?= Html::encode($title) . "\n" ?>
  </h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <?= Html::tag(
    'p',
    implode('<br>', [
      Html::encode(
        Yii::t('app', 'The data is not something you will know immediately after downloading.'),
      ),
      Html::encode(
        Yii::t(
          'app',
          'The data is intended to be analyzed using spreadsheet software (Excel, etc.) or programs to analyze.',
        ),
      ),
    ]),
  ) . "\n" ?>

  <h2><?= Html::encode(Yii::t('app', 'Splatoon 3')) ?></h2>
  <ul>
    <li>
      <?= Icon::fileCsv() . "\n" ?>
      <?= Html::a(
        Html::encode(Yii::t('app', 'Battle results (CSV)')),
        'https://dl-stats.stats.ink/splatoon-3/battle-results-csv/',
        ['target' => '_blank', 'rel' => 'noopener nofollow'],
      ) . "\n" ?>
      /
      <?= Html::a(
        Html::encode(Yii::t('app', 'Schema')),
        'https://github.com/fetus-hina/stat.ink/wiki/Spl3-%EF%BC%8D-CSV-Schema-%EF%BC%8D-Battle',
        [
          'target' => '_blank',
          'rel' => 'noreferrer noopener nofollow',
        ],
      ) . "\n" ?>
    </li>
    <li>
      <?= Icon::fileCsv() . "\n" ?>
      <?= Html::a(
        Html::encode(Yii::t('app', 'Salmon Run results (CSV)')),
        'https://dl-stats.stats.ink/splatoon-3/salmon-results-csv/',
        ['target' => '_blank', 'rel' => 'noopener nofollow'],
      ) . "\n" ?>
      /
      <?= Html::a(
        Html::encode(Yii::t('app', 'Schema')),
        'https://github.com/fetus-hina/stat.ink/wiki/Spl3-%EF%BC%8D-CSV-Schema-%EF%BC%8D-Salmon',
        [
          'target' => '_blank',
          'rel' => 'noreferrer noopener nofollow',
        ],
      ) . "\n" ?>
    </li>
  </ul>

  <h2><?= Html::encode(Yii::t('app', 'Splatoon 2')) ?></h2>
  <ul>
    <li>
      <?= Icon::fileCsv() . "\n" ?>
      <?= Html::a(
        Html::encode(Yii::t('app', 'Battle results (CSV)')),
        'https://dl-stats.stats.ink/splatoon-2/battle-results-csv/',
        ['target' => '_blank', 'rel' => 'noopener nofollow'],
      ) . "\n" ?>
      /
      <?= Html::a(
        Html::encode(Yii::t('app', 'Schema')),
        'https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/download-battle-csv.md',
        [
          'target' => '_blank',
          'rel' => 'noreferrer noopener nofollow',
        ],
      ) . "\n" ?>
    </li>
  </ul>

  <h2><?= Html::encode(Yii::t('app', 'Splatoon')) ?></h2>
  <p>
    データファイルをその場で生成してからダウンロード処理が行われます。
    クリックしてからしばらく時間がかかりますが、連打しないでください。
  </p>

  <p>
    各言語や文字コードは、ブキやステージの名前のローカライズ部分にのみ影響します。（どれを落としても本質的な情報は同じです）
  </p>

  <ul>
    <li>
      <?= Icon::fileCsv() . "\n" ?>
      ブキ・ルール・ステージ別にバトル数・勝率を集計したもの (CSV)
      <?= $this->render('_dl-langs', ['route' => 'download-stats/weapon-rule-map']) . "\n" ?>
    </li>
  </ul>
</div>
