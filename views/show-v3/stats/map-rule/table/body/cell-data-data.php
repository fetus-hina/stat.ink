<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
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
 * @var array $stats
 */

echo Html::tag(
  'div',
  implode('', [
    $this->render('cell-data/n', array_merge(compact('map', 'rule', 'user'), ['n' => (int)($stats['battles'] ?? 0)])),
    $this->render('cell-data/kd', compact('stats')),
  ]),
  ['class' => 'mb-2'],
);
