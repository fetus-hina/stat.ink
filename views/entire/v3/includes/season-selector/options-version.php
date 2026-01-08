<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\SplatoonVersion3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SplatoonVersion3[] $versions
 * @var SplatoonVersion3|null $version
 * @var View $this
 * @var callable(SplatoonVersion3): string $versionUrl
 */

echo implode(
  '',
  ArrayHelper::getColumn(
    $versions,
    fn (SplatoonVersion3 $model): string => Html::tag(
      'option',
      Html::encode(
        preg_match('/^v\d+/', $model->name)
          ? Yii::t('app', 'Version {0}', [ltrim($model->name, 'v')])
          : Yii::t('app-version3', $model->name),
      ),
      [
        'selected' => $model->tag === $version?->tag,
        'value' => $versionUrl($model),
      ],
    ),
  ),
);
