<?php
use statink\yii2\stages\spl2\Spl2Stage;
use yii\helpers\Html;
?>
<div class="row">
<?php foreach ($schedules as $schedule): ?>
<?php $weapons = array_slice(
    array_merge($schedule->weapons, [null, null, null, null]),
    0,
    4
) ?>
  <div class="col-xs-12 col-md-6">
    <h3><?= Html::encode(sprintf(
      '[%s - %s]',
      Yii::$app->formatter->asDateTime($schedule->start_at, 'short'),
      Yii::$app->formatter->asDateTime($schedule->end_at, 'short')
    )) ?></h3>
    <ul class="battles maps salmon-schedule">
      <li>
        <div class="thumbnail thumbnail-salmon">
          <div class="row">
            <div class="col-xs-6">
              <?= Spl2Stage::img('daytime', $schedule->map->key, [
                'class' => 'img-responsive',
              ]) . "\n" ?>
              <div class="battle-data">
                <?= Html::encode(Yii::t('app-salmon-map2', $schedule->map->name)) . "\n" ?>
              </div>
            </div>
            <div class="col-xs-6">
<?php foreach ($weapons as $weapon): ?>
<?php if ($weapon): ?>
                * <?= Html::encode(Yii::t('app-weapon2', $weapon->weapon->name)) ?><br>
<?php else: ?>
                * <?= Html::encode(Yii::t('app-salmon2', 'Random')) ?><br>
<?php endif ?>
<?php endforeach ?>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
<?php endforeach ?>
</div>
