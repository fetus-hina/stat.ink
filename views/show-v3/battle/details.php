<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;
use app\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var Battle3 $model
 * @var User $user
 * @var View $this
 */

echo DetailView::widget([
  'model' => $model,
  'id' => 'battle',
  'options' => [
    'class' => ['table', 'table-striped'],
  ],
  'template' => function (array $attribute, $index, Widget $widget): ?string {
    if ($attribute['value'] === null) {
      return null;
    }
    $captionOptions = Html::renderTagAttributes(
      ArrayHelper::getValue($attribute, 'captionOptions', [])
    );
    $contentOptions = Html::renderTagAttributes(
      ArrayHelper::getValue($attribute, 'contentOptions', [])
    );
    return strtr(
      '<tr><th{captionOptions}>{label}</th><td{contentOptions}>{value}</td></tr>',
      [
        '{label}' => $attribute['label'],
        '{value}' => $widget->formatter->format($attribute['value'], $attribute['format']),
        '{captionOptions}' => $captionOptions,
        '{contentOptions}' =>  $contentOptions,
      ]
    );
  },
  'attributes' => [
    require __DIR__ . '/details/rule.php',
    require __DIR__ . '/details/event.php',
    require __DIR__ . '/details/map.php',
    require __DIR__ . '/details/weapon.php',
    // freshness
    require __DIR__ . '/details/rank.php',
    require __DIR__ . '/details/level.php',
    require __DIR__ . '/details/result.php',
    require __DIR__ . '/details/rank-up-battle.php',
    require __DIR__ . '/details/challenge.php',
    require __DIR__ . '/details/x-progress.php',
    require __DIR__ . '/details/x-power.php',
    require __DIR__ . '/details/x-power-chart.php',
    require __DIR__ . '/details/bankara-power.php',
    require __DIR__ . '/details/clout.php',
    require __DIR__ . '/details/fest-power.php',
    require __DIR__ . '/details/event-power.php',
    require __DIR__ . '/details/team-inked.php',
    require __DIR__ . '/details/team-count.php',
    require __DIR__ . '/details/rank-in-team.php',
    require __DIR__ . '/details/kill-death.php',
    require __DIR__ . '/details/special.php',
    require __DIR__ . '/details/signal.php',
    require __DIR__ . '/details/inked.php',
    require __DIR__ . '/details/medal.php',
    // cause-of-death
    require __DIR__ . '/details/cash.php',
    require __DIR__ . '/details/link-url.php',
    require __DIR__ . '/details/replay-code.php',
    require __DIR__ . '/details/season.php',
    require __DIR__ . '/details/period.php',
    require __DIR__ . '/details/start-at.php',
    require __DIR__ . '/details/end-at.php',
    require __DIR__ . '/details/elapsed-time.php',
    require __DIR__ . '/details/created-at.php',
    require __DIR__ . '/details/user-agent.php',
    require __DIR__ . '/details/user-agent-variables.php',
    require __DIR__ . '/details/public-note.php',
    require __DIR__ . '/details/private-note.php',
    require __DIR__ . '/details/game-version.php',
    require __DIR__ . '/details/stats.php',
  ],
]);
