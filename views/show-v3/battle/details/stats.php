<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\AutoTooltipAsset;
use app\components\helpers\Battle3EntireStatsHelper;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\Battle3;
use yii\bootstrap\Html;
use yii\web\View;

return [
  'label' => Yii::t('app', 'Stats'),
  'format' => 'raw',
  'value' => function (Battle3 $model): string {
    /**
     * @param string[] $lines
     */
    $tooltip = function (string $html, array $lines): string {
      AutoTooltipAsset::register(TypeHelper::instanceOf(Yii::$app->view, View::class));
      return Html::tag('abbr', $html, [
        'class' => 'auto-tooltip',
        'data' => ['html' => 'true'],
        'title' => implode('<br>', array_map(Html::encode(...), $lines)),
      ]);
    };

    $lobby = $model->lobby;
    $weapon = $model->weapon;
    $map = $model->map;
    $result = $model->result;
    if (!$lobby || !$weapon || !$map || !$result) {
      return implode('', [
        Html::tag('span', Icon::no(), ['class' => 'text-danger']),
        $tooltip(
          Html::encode(Yii::t('app-battle', 'Incomplete Data')),
          [
            Yii::t('app-battle', 'The following data is missing:'),
            ...array_filter([
              $lobby ? null : Yii::t('app', 'Lobby'),
              $weapon ? null : Yii::t('app', 'Weapon'),
              $map ? null : Yii::t('app', 'Stage'),
              $result ? null : Yii::t('app-results', 'Result'),
            ]),
          ],
        ),
      ]);
    }

    if ($lobby->key === 'private') {
      return implode('', [
        Html::tag('span', Icon::no(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app-lobby3', 'Private Battle')),
      ]);
    }

    if (!$result->aggregatable) {
      return implode('', [
        Html::tag('span', Icon::no(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app-results', $result->name)),
      ]);
    }

    if ($model->has_disconnect) {
      return implode('', [
        Html::tag('span', Icon::no(), ['class' => 'text-danger']),
        Html::encode(Yii::t('app-battle', 'Disconnected')),
      ]);
    }

    $f = function (string $label, bool $value, array $reasons = []) use ($tooltip): string {
      $text = Html::encode(Yii::t('yii', $value ? 'Yes' : 'No'));
      if ($reasons) {
        $text = $tooltip(
          $text,
          array_map(
            fn (string $reason): string => match ($reason) {
              Battle3EntireStatsHelper::REASON_NOT_AUTOMATED => Yii::t(
                'app-battle',
                'Only battles uploaded automatically are used.',
              ),
              Battle3EntireStatsHelper::REASON_INCOMPLETE_DATA => Yii::t(
                'app-battle',
                'Some required data, such as the mode, is missing.',
              ),
              Battle3EntireStatsHelper::REASON_NO_START_TIME => Yii::t(
                'app-battle',
                'The battle start time is unknown.',
              ),
              Battle3EntireStatsHelper::REASON_FUTURE_START_TIME => Yii::t(
                'app-battle',
                'The battle start time is later than the upload time.',
              ),
              Battle3EntireStatsHelper::REASON_UPLOADED_TOO_LATE => Yii::t(
                'app-battle',
                'The battle was uploaded too long after it was played.',
              ),
              Battle3EntireStatsHelper::REASON_UNKNOWN_EVENT => Yii::t(
                'app-battle',
                'The challenge could not be identified.',
              ),
              Battle3EntireStatsHelper::REASON_INVALID_PLAYERS => Yii::t(
                'app-battle',
                'The player data is missing or incomplete.',
              ),
              default => $reason,
            },
            $reasons,
          ),
        );
      }

      return vsprintf('%s: %s%s', [
        Html::encode($label),
        $value
          ? Html::tag('span', Icon::yes(), ['class' => 'text-success'])
          : Html::tag('span', Icon::no(), ['class' => 'text-danger']),
        $text,
      ]);
    };

    $usedInEntire = $model->is_automated && $model->use_for_entire;
    return implode('<br>', [
      $f(Yii::t('app-battle', 'Automated'), $model->is_automated),
      $f(
        Yii::t('app-battle', 'Used in global stats'),
        $usedInEntire,
        $usedInEntire ? [] : Battle3EntireStatsHelper::getExclusionReasons($model),
      ),
    ]);
  },
];
