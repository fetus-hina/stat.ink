<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\components\widgets\Label;
use app\models\Battle3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $model
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
              $result = $model->result;
              if (!$result || $result->is_win === null) {
                return Html::tag(
                  'div',
                  Html::encode(Yii::t('app', $result->name ?? '?')),
                  ['class' => 'simple-battle-result simple-battle-result-unk']
                );
              }
              return Html::tag(
                'div',
                implode('<br>', array_filter([
                  Html::encode(Yii::t('app', $result->name)),
                  $result->key !== 'draw' && $model->is_knockout !== null
                    ? Html::encode($model->is_knockout ? Yii::t('app', 'K.O.') : Yii::t('app', 'Time'))
                    : '',
                ])),
                [
                  'class' => [
                    'simple-battle-result',
                    $result->key === 'draw'
                      ? 'simple-battle-result-unk'
                      : ($result->is_win ? 'simple-battle-result-won' : 'simple-battle-result-lost'),
                  ],
                ]
              );
            })(),
            // }}}
            // 詳細
            Html::tag(
              'div',
              implode('', [
                Html::tag(
                  'div',
                  Html::encode(Yii::t('app-map3', $model->map->name ?? '?')),
                  ['class' => 'simple-battle-rule omit']
                ),
                Html::tag(
                  'div',
                  implode(' ', [
                    Icon::s3Rule($model->rule),
                    Html::encode(Yii::t('app-rule3', $model->rule->name ?? '?')),
                    Html::encode('-'),
                    Icon::s3Lobby($model->lobby),
                    Html::encode(Yii::t('app-lobby3', $model->lobby->name ?? '?')),
                  ]),
                  ['class' => 'simple-battle-rule omit'],
                ),
                Html::tag(
                  'div',
                  $model->weapon
                    ? implode(' ', [
                      Icon::s3Weapon($model->weapon),
                      Html::encode(Yii::t('app-weapon3', $model->weapon->name)),
                      Icon::s3Subweapon($model->weapon->subweapon),
                      Icon::s3Special($model->weapon->special),
                    ])
                    : '?',
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
        Html::tag(
          'div',
          $model->has_disconnect ? Icon::hasDisconnected() : '',
          ['class' => 'simple-battle-disconnected text-danger'],
        ),
      ]),
      ['class' => 'simple-battle-row-impl']
    ),
    ['show-v3/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->uuid]
  ),
  ['class' => 'simple-battle-row', 'data-period' => $model->period]
); ?>
