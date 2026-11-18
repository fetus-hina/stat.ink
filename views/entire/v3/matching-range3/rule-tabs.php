<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3|null $rule
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var callable(Rule3|null): string $ruleUrl
 */

$items = [null, ...array_values($rules)];

?>
<nav class="mb-1">
  <?= Html::tag(
    'ul',
    implode(
      '',
      array_map(
        function (?Rule3 $item) use ($rule, $ruleUrl): string {
          $isActive = $item?->key === $rule?->key;
          return Html::tag(
            'li',
            Html::tag(
              'a',
              $item
                ? trim(
                  implode(' ', [
                    Icon::s3Rule($item),
                    Html::tag(
                      'span',
                      Html::encode(Yii::t('app-rule3', $item->name)),
                      ['class' => 'd-none d-sm-inline'],
                    ),
                  ]),
                )
                : Html::encode(Yii::t('app-entire', 'All')),
              $isActive ? [] : ['href' => $ruleUrl($item)],
            ),
            [
              'role' => 'presentation',
              'class' => $isActive ? 'active' : null,
            ],
          );
        },
        $items,
      ),
    ),
    ['class' => 'nav nav-pills'],
  ) . "\n" ?>
</nav>
