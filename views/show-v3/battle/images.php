<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\PhotoSwipeSimplifyAsset;
use app\models\BattleImageGear3;
use app\models\BattleImageJudge3;
use app\models\BattleImageResult3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var array<BattleImageGear3|BattleImageJudge3|BattleImageResult3|null> $images
 */

$htmlImgs = array_values(
  array_filter(
    ArrayHelper::getColumn(
      $images,
      function (BattleImageGear3|BattleImageJudge3|BattleImageResult3|null $model): ?string {
        if (!$model || !$model->filename) {
          return null;
        }

        $url = Url::to(sprintf('@imageurl/%s', $model->filename), true);
        return Html::tag(
          'div',
          Html::a(
            Html::img($url, ['class' => 'w-100 h-auto']),
            $url,
          ),
          ['class' => 'col-xs-12 col-md-6 image-container'],
        );
      },
    ),
    fn (?string $html): bool => $html !== null,
  ),
);

if ($htmlImgs) {
  PhotoSwipeSimplifyAsset::register($this);

  echo Html::tag(
    'div',
    implode('', array_slice($htmlImgs, 0, 2)),
    [
      'class' => 'row',
      'data' => [
        'pswp' => '',
      ],
    ],
  );
}
