<?php
use app\assets\MapImage2Asset;
use yii\helpers\Html;

$am = Yii::$app->assetManager;
$img2asset = $am->getBundle(MapImage2Asset::class);

if ($data):
?>
<?= Html::tag('h3', implode('', [
  Html::encode(Yii::t('app-rule2', $mode)),
  ' &raquo; ',
  Html::encode(Yii::t('app-rule2', $data->rule->name)),
])) . "\n" ?>
<ul class="battles maps">
<?php foreach ($data->maps as $_): ?>
  <li>
    <?= Html::tag(
      'div',
      implode('', [
        Html::img($am->getAssetUrl($img2asset, 'daytime/' . $_->key . '.jpg')),
        Html::tag(
          'div',
          Html::encode(Yii::t('app-map2', $_->name)),
          ['class' => 'battle-data']
        ),
      ]),
      ['class' => ['thumbnail', 'thumbnail-' . $data->rule->key]]
    ) . "\n" ?>
  </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
