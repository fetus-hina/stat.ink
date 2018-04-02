<?php
use app\assets\MapImage2Asset;
use yii\helpers\Html;

$timeZone = new DateTimeZone(Yii::$app->timeZone);

$timeFormat = (Yii::$app->language === 'en-US')
  ? 'ga'
  : 'H:i';

$assetManager = Yii::$app->assetManager;
$map2Asset = MapImage2Asset::register($this);
?>
<div class="row">
<?php foreach ($data as $schedule): ?>
<?php if ($schedule['term'] && $schedule['data']): ?>
<?php $t1 = (new DateTimeImmutable())->setTimestamp($schedule['term'][0])->setTimeZone($timeZone) ?>
<?php $t2 = (new DateTimeImmutable())->setTimestamp($schedule['term'][1])->setTimeZone($timeZone) ?>
  <div class="col-xs-12 col-md-6">
    <h3><?= Html::encode(implode(' ', [
      '[' . $t1->format($timeFormat) . '-' . $t2->format($timeFormat) . ']',
      Yii::t('app-rule2', $schedule['data']->rule->name)
    ])) ?></h3>
    <ul class="battles maps">
<?php foreach ($schedule['data']->maps as $map): ?>
      <li>
        <?= Html::beginTag('div', ['class' => ['thumbnail', 'thumbnail-' . $schedule['data']->rule->key]]) . "\n" ?>
          <?= Html::img(
            $assetManager->getAssetUrl($map2Asset, 'daytime/' . $map->key . '.jpg'),
            []
          ) . "\n" ?>
          <div class="battle-data">
            <?= Html::encode(
              Yii::t('app-map2', $map->name)
            ) . "\n" ?>
          </div>
        </div>
      </li>
<?php endforeach ?>
    </ul>
  </div>
<?php endif ?>
<?php endforeach ?>
</div><?php
