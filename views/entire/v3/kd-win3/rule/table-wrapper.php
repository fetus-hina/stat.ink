<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\RatioAsset;
use app\assets\TableResponsiveForceAsset;
use app\models\Rule3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var View $this
 * @var array<int, array<int, array{battles: int, wins: int}>> $data
 * @var string|null $lobbyKey
 */

if ($rule->key === 'nawabari') {
  if (
    $lobbyKey === 'xmatch' ||
    $lobbyKey === 'league' ||
    str_starts_with((string)$lobbyKey, 'bankara_')
  ) {
    echo Html::tag('p', Html::encode(Yii::t('app', 'N/A')));
    return;
  }
} else {
  if ($lobbyKey === 'regular' || str_starts_with((string)$lobbyKey, 'splatfest')) {
    echo Html::tag('p', Html::encode(Yii::t('app', 'N/A')));
    return;
  }
}

RatioAsset::register($this);
TableResponsiveForceAsset::register($this);

$fmt = Yii::$app->formatter;

$width = 100.0 / (20 + 1);

$this->registerCss(
  Html::renderCss([
    '.table-responsive .rule-table td,.table-responsive .rule-table th' => [
      'width' => "{$width}%",
      'min-width' => '2.5em',
    ],
  ]),
);

$this->registerCss(
  '@media screen and (min-width:768px){.table-responsive table.rule-table{table-layout:fixed}}',
);

$fragmentId = hash_hmac(
  'sha256',
  Json::encode($data),
  vsprintf('%s?%s', [
    __FILE__,
    http_build_query([
      'language' => Yii::$app->language,
      'version' => 2,
    ]),
  ]),
);

if ($this->beginCache($fragmentId, ['duration' => 6 * 3600])) {
  echo Html::tag(
    'div',
    $this->render('table', compact('data', 'lobbyKey', 'rule')),
    ['class' => 'table-responsive table-responsive-force'],
  );
  $this->endCache();
}
