<?php

declare(strict_types=1);

use app\assets\SimpleBattleListAsset;
use app\components\widgets\KillRatioBadgeWidget;
use app\models\Battle;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Battle $model
 * @var View $this
 */

SimpleBattleListAsset::register($this);

$f = Yii::$app->formatter;
?>
<?= Html::beginTag('li', [
    'class' => 'simple-battle-row',
    'data' => [
      'period' => $model->period,
    ],
  ]
) . "\n" ?>
  <?= Html::beginTag('a', [
      'href' => Url::to([
        'show/battle',
          'screen_name' => $model->user->screen_name,
          'battle' => $model->id,
      ]),
    ]
  ) . "\n" ?>
    <div class="simple-battle-row-impl">
      <div class="simple-battle-row-impl-main">
<?php if ($model->is_win === null) { ?>
        <div class="simple-battle-result simple-battle-result-unk">?</div>
<?php } else { ?>
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
<?php } ?>
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
      <div class="simple-battle-at"><?=
        $model->end_at ? $f->asHtmlDatetime($model->end_at, 'short') : ''
      ?></div>
    </div>
  </a>
</li>
