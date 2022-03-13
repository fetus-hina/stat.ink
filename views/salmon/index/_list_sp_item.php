<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\components\i18n\Formatter;
use app\components\widgets\Label;
use app\models\Salmon2;
use app\models\User;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Formatter $formatter
 * @var Salmon2 $model
 * @var User $user
 * @var View $this
 */

$myData = $model->myData;

?>
<?= Html::beginTag('a', [
  'href' => Url::to(['salmon/view',
    'screen_name' => $user->screen_name,
    'id' => $model->id,
  ]),
]) . "\n" ?>
  <div class="simple-battle-row-impl">
    <div class="simple-battle-row-impl-main">
      <?= Html::tag(
        'div',
        $model->clear_waves === null
          ? '?'
          : ($model->clear_waves >= 3
            ? Html::encode(Yii::t('app-salmon2', 'Cleared'))
            : implode('', [
              Yii::t('app-salmon2', 'Failed<br><small>in wave {waveNumber}</small>', [
                'waveNumber' => $model->clear_waves + 1,
              ]),
              $model->fail_reason_id
                ? Html::tag('div', Label::widget([
                  'content' => Yii::t('app-salmon2', $model->failReason->short_name),
                  'color' => $model->failReason->color,
                  'options' => [
                    'style' => [
                      'font-size' => '11px',
                      'font-weight' => 'normal',
                    ],
                  ],
                ]))
                : '',
            ])
          ),
        ['class' => [
          'simple-battle-result',
          $model->clear_waves === null
            ? 'simple-battle-result-unk'
            : ($model->clear_waves >= 3
              ? 'simple-battle-result-won'
              : 'simple-battle-result-lost'
            )
        ]]
      ) . "\n" ?>
      <div class="simple-battle-data">
        <div class="simple-battle-rule omit">
          <?= Html::encode(
            Yii::t(
              'app-salmon-map2',
              $model->stage_id
                ? $model->stage->name
                : '?'
            )
          ) . "\n" ?>
        </div>
        <div class="simple-battle-weapon omit">
          <?= Html::encode(
            Yii::t(
              'app-special2',
              $myData && $myData->special_id
                ? $myData->special->name
                : '?'
            )
          ) . "\n" ?>
        </div>
        <div class="simple-battle-rule omit">
          <?= Html::encode(sprintf(
            '%s: %s',
            Yii::t('app-salmon2', 'Hazard Level'),
            $formatter->asDecimal($model->danger_rate, 1)
          )) . "\n" ?> 
        </div>
        <div class="simple-battle-kill-death omit">
          <?= Html::encode(sprintf(
            '%s: %s, %s: %s',
            Yii::t('app-salmon2', 'Golden Eggs'),
            $formatter->asInteger($myData->golden_egg_delivered ?? null),
            Yii::t('app-salmon2', 'Power Eggs'),
            $formatter->asInteger($myData->power_egg_collected ?? null)
          )) . "\n" ?>
        </div>
      </div>
    </div>
    <div class="simple-battle-at">
      <?= $formatter->asHtmlDatetime(
        $model->start_at ?? $model->end_at ?? $model->created_at ?? null,
        'medium'
      ) . "\n" ?>
    </div>
  </div>
</a>
