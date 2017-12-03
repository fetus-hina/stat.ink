<?php
use app\assets\SwipeboxRunnerAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;

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
          $image->url,
          ['class' => 'swipebox']
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
  SwipeboxRunnerAsset::register($this);
  echo Html::tag(
    'div',
    implode('', array_slice($parts, 0, 2)),
    ['class' => 'row']
  );
}
