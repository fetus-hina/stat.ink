<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\widgets\Label;
use app\models\Battle2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle2 $model
 * @var View $this
 */

?>
<?= Html::tag(
  'li',
  Html::a(
    Html::tag(
      'div',
      implode('', [
        Html::tag(
          'div',
          implode('', [
            // 勝敗表示 {{{
            (function () use ($model) {
              if ($model->is_win === null) {
                return Html::tag(
                  'div',
                  Html::encode('?'),
                  ['class' => 'simple-battle-result simple-battle-result-unk']
                );
              }
              return Html::tag(
                'div',
                implode('', [
                  Html::encode($model->is_win ? Yii::t('app', 'Won') : Yii::t('app', 'Lost')),
                  ($model->isGachi && $model->is_knockout !== null)
                    ? ('<br>' . Html::encode($model->is_knockout ? Yii::t('app', 'K.O.') : Yii::t('app', 'Time')))
                    : '',
                ]),
                ['class' => [
                  'simple-battle-result',
                  $model->is_win ? 'simple-battle-result-won' : 'simple-battle-result-lost',
                ]]
              );
            })(),
            // }}}
            // 詳細
            Html::tag(
              'div',
              implode('', [
                Html::tag(
                  'div',
                  Html::encode(Yii::t('app-map2', $model->map->name ?? '?')),
                  ['class' => 'simple-battle-rule omit']
                ),
                Html::tag(
                  'div',
                  implode(' ', array_filter(
                    [
                      Html::encode(Yii::t('app-rule2', $model->rule->name ?? '?')),
                      $model->specialBattle
                        ? Label::widget([
                          'content' => Yii::t('app', $model->specialBattle->name),
                          'color' => 'primary',
                        ])
                        : null,
                    ],
                    function (?string $value): bool {
                      return $value !== null;
                    }
                  )),
                  ['class' => 'simple-battle-rule omit']
                ),
                Html::tag(
                  'div',
                  Html::encode(Yii::t('app-weapon2', $model->weapon->name ?? '?')),
                  ['class' => 'simple-battle-weapon omit']
                ),
                Html::tag(
                  'div',
                  (function () use ($model) {
                    if ($model->kill !== null && $model->death !== null) {
                      return sprintf(
                        '%s+%sK / %sD / %sS %s',
                        Html::encode(Yii::$app->formatter->asInteger((int)$model->kill)),
                        Html::encode(
                          $model->kill_or_assist !== null
                            ? Yii::$app->formatter->asInteger($model->kill_or_assist - $model->kill)
                            : '?'
                        ),
                        Html::encode(Yii::$app->formatter->asInteger((int)$model->death)),
                        Html::encode(
                          $model->special !== null
                            ? Yii::$app->formatter->asInteger($model->special)
                            : '?'
                        ),
                        (function (int $k, int $d) : string {
                          if ($k === $d) {
                            return Html::tag('span', Html::encode('='), ['class' => 'label label-default']);
                          } elseif ($k > $d) {
                            return Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
                          } else {
                            return Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
                          }
                        })($model->kill, $model->death)
                      );
                    }
                    if ($model->kill_or_assist !== null) {
                      return sprintf(
                        '%s: %s',
                        Html::encode(Yii::t('app', 'Kill or Assist')),
                        Html::encode(Yii::$app->formatter->asInteger($model->kill_or_assist))
                      );
                    }
                    return '';
                  })(),
                  ['class' => 'simple-battle-kill-death omit']
                ),
              ]),
              ['class' => 'simple-battle-data']
            ),
          ]),
          ['class' => 'simple-battle-row-impl-main']
        ),
        Html::tag(
          'div',
           Html::encode(
             $model->end_at
               ? Yii::$app->formatter->asDatetime($model->end_at, 'short')
               : ''
          ),
          ['class' => 'simple-battle-at']
        ),
      ]),
      ['class' => 'simple-battle-row-impl']
    ),
    ['show-v2/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->id]
  ),
  ['class' => 'simple-battle-row', 'data-period' => $model->period]
); ?>
