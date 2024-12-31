<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Season3;
use app\models\SplatoonVersion3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Season3[] $seasons
 * @var Season3|null $season
 * @var SplatoonVersion3[]|null $versions
 * @var SplatoonVersion3|null $version
 * @var View $this
 * @var callable(Season3): string $seasonUrl
 * @var callable(SplatoonVersion3): string|null $versionUrl
 */

if (!isset($versions)) {
  $versions = [];
}

if (!isset($version)) {
  $version = null;
}

if (!isset($versionUrl)) {
  $versionUrl = null;
}

echo Html::tag(
  'select',
  $versions && $versionUrl
    ? implode('', [
      Html::tag(
        'optgroup',
        $this->render('season-selector/options-season', compact('season', 'seasons', 'seasonUrl')),
        ['label' => Yii::t('app', 'Season')],
      ),
      Html::tag(
        'optgroup',
        $this->render('season-selector/options-version', compact('version', 'versions', 'versionUrl')),
        ['label' => Yii::t('app', 'Version')],
      ),
    ])
    : $this->render('season-selector/options-season', compact('season', 'seasons', 'seasonUrl')),
  [
    'class' => 'form-control m-0',
    'onchange' => 'window.location.href = this.value',
  ],
);
