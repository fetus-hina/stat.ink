<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var View $this
 * @var array<int, array<int, array{battles: int, wins: int}>> $data
 * @var string|null $lobbyKey,
 */

echo Html::tag(
  'div',
  implode('', [
    $this->render('../includes/rule-header', [
      'id' => $rule->key,
      'rule' => $rule,
    ]),
    $this->render('rule/summary', compact('data')),
    $this->render('rule/table-wrapper', compact('data', 'lobbyKey', 'rule')),
  ]),
  ['class' => 'mb-3'],
);
