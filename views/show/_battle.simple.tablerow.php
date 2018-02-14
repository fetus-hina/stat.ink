<?php
use app\assets\AppOptAsset;
use app\components\widgets\KillRatioBadgeWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$asset = AppOptAsset::register($this);
$asset->registerCssFile($this, 'battles-simple.css');

$f = Yii::$app->formatter;
?>
<?= Html::beginTag('li', ['class' => 'simple-battle-row', 'data-period' => $model->period]) . "\n" ?>
  <?= Html::beginTag('a', ['href' => Url::to(['show/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->id])]) . "\n" ?>
    <div class="simple-battle-row-impl">
      <div class="simple-battle-row-impl-main">
<?php if ($model->is_win === null): ?>
        <div class="simple-battle-result simple-battle-result-unk">?</div>
<?php else: ?>
        <?= Html::tag(
          'div',
          implode('<br>', array_filter([
            Html::encode($model->is_win ? Yii::t('app', 'Won') : Yii::t('app', 'Lost')),
            ($model->isGachi && $model->is_knock_out !== null)
              ? Html::encode($model->is_knock_out ? Yii::t('app', 'K.O.') : Yii::t('app', 'Time'))
              : null,
          ])),
          ['class' => [
            'simple-battle-result',
            $model->is_win
              ? 'simple-battle-result-won'
              : 'simple-battle-result-lost',
          ]]
        ) . "\n" ?>
<?php endif ?>
        <div class="simple-battle-data">
          <div class="simple-battle-map omit"><?=
            Html::encode($model->map ? Yii::t('app-map', $model->map->name) : '?')
          ?></div>
          <div class="simple-battle-rule omit"><?=
            Html::encode($model->rule ? Yii::t('app-rule', $model->rule->name) : '?')
          ?></div>
          <div class="simple-battle-weapon omit"><?=
            Html::encode($model->weapon ? Yii::t('app-weapon', $model->weapon->name) : '?')
          ?></div>
          <div class="simple-battle-kill-death omit"><?=
            implode(' ', array_filter([
              Html::encode(sprintf(
                '%sK / %sD',
                $model->kill === null ? '?' : $f->asInteger($model->kill),
                $model->death === null ? '?' : $f->asInteger($model->death)
              )),
              ($model->kill !== null && $model->death !== null)
                ? KillRatioBadgeWidget::widget([
                  'kill' => $model->kill,
                  'death' => $model->death,
                ])
                : null
            ]))
          ?></div>
        </div>
      </div>
      <div class="simple-battle-at">
<?php if ($model->end_at): ?>
<?php $t = new DateTimeImmutable($model->end_at, new DateTimeZone(Yii::$app->timeZone)) ?>
        <?= Html::tag(
          'time', 
          Html::encode($f->asDateTime($t, 'short')),
          ['datetime' => $t->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM)]
        ) . "\n" ?>
<?php endif ?>
      </div>
    </div>
  </a>
</li><?php
?>
