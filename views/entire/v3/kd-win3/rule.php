<?php

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
    $this->render('rule/heading', compact('rule')),
    $this->render('rule/summary', compact('data')),
    $this->render('rule/table-wrapper', compact('data', 'lobbyKey', 'rule')),
  ]),
  ['class' => 'mb-3'],
);
