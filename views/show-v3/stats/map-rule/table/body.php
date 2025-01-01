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
 * @var User $user
 * @var View $this
 * @var array<string, Map3> $maps
 * @var array<string, Rule3> $rules
 * @var array<string, array<string, array>> $mapStats
 * @var array<string, array> $totalStats
 */

echo Html::tag(
  'tbody',
  implode('', [
    $this->render('body/summary', compact('rules', 'totalStats', 'user')),
    $this->render('body/maps-rules', compact('mapStats', 'maps', 'rules', 'user')),
  ]),
);
