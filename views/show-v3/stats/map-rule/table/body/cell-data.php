<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3 $map
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 * @var array|null $stats
 */

if (!$stats) {
  echo Html::tag('td', '');
  return;
}

echo Html::tag(
  'td',
  implode('', [
    $this->render('cell-data-chart', compact('stats')),
    $this->render('cell-data-data', compact('map', 'rule', 'stats', 'user')),
  ]),
);
