<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\PhotoSwipeSimplifyAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 */

$parts = array_filter(
  array_map(
    function ($image) : ?string {
      if (!$image || !$image->url) {
        return null;
      }
      return Html::tag(
        'div',
        Html::a(
          Html::img(
            $image->url,
            ['style' => [
              'width' => '100%',
              'height' => 'auto',
            ]]
          ),
          $image->url
        ),
        ['class' => 'col-xs-12 col-md-6 image-container']
      );
    },
    $images
  ),
  function (?string $html) : bool {
    return $html !== null;
  }
);

if ($parts) {
  PhotoSwipeSimplifyAsset::register($this);
  echo Html::tag(
    'div',
    implode('', array_slice($parts, 0, 2)),
    [
      'class' => 'row',
      'data-pswp' => '',
    ]
  );
}
