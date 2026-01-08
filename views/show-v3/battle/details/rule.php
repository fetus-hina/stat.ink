<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Lobby3;
use app\models\Rule3;
use yii\base\Model;
use yii\helpers\Html;

$render = function (?Model $model, string $catalog): string {
  if (!$model) {
    return Html::encode('?');
  }

  return trim(
    implode(' ', [
      match ($model::class) {
        Lobby3::class => Icon::s3Lobby($model),
        Rule3::class => Icon::s3Rule($model),
        default => '',
      },
      Html::encode(Yii::t($catalog, $model->name)),
    ]),
  );
};

return [
  'label' => Yii::t('app', 'Mode'),
  'format' => 'raw',
  'value' => function (Battle3 $model) use ($render): ?string {
    $rule = $model->rule;
    $lobby = $model->lobby;
    if (!$rule && !$lobby) {
      return null;
    }

    return trim(
      vsprintf('%s %s', [
        ($rule && $lobby)
          ? Html::a(
            Icon::search(),
            ['/show-v3/user',
              'screen_name' => $model->user->screen_name,
              'f' => [
                'lobby' => $lobby->key,
                'rule' => $rule->key,
              ],
            ],
          )
          : '',
        vsprintf('%s - %s', [
          $render($rule, 'app-rule3'),
          $render($lobby, 'app-lobby3'),
        ]),
      ]),
    );
  },
];
