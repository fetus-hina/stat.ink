<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\SalmonBoss3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, SalmonBoss3> $bosses
 * @var array<string, array{boss_key: string, appearances: int, defeated: int, defeated_by_me: int}> $stats
 */

return [
  'contentOptions' => function (array $row) use ($bosses): array {
    $key = TypeHelper::string(ArrayHelper::getValue($row, 'boss_key'));
    $boss = ArrayHelper::getValue($bosses, $key);
    if (!$boss instanceof SalmonBoss3) {
      return [];
    }

    return [
      'data' => [
        'sort-value' => Yii::t('app-salmon-boss3', $boss->name),
      ],
    ];
  },
  'encodeLabel' => false,
  'format' => 'raw',
  'headerOptions' => [
    'class' => 'text-center',
    'data' => [
      'sort' => 'string',
      'sort-default' => 'asc',
    ],
  ],
  'label' => Html::tag(
    'span',
    Html::encode(Yii::t('app-salmon3', 'Boss Salmonid')),
    ['class' => 'd-none d-md-inline'],
  ),
  'value' => function (array $row) use ($bosses): string {
    $key = TypeHelper::string(ArrayHelper::getValue($row, 'boss_key'));
    $boss = ArrayHelper::getValue($bosses, $key);
    return $boss instanceof SalmonBoss3
      ? implode(' ', [
        Icon::s3BossSalmonid($boss),
        Html::encode(Yii::t('app-salmon-boss3', $boss->name))
      ])
      : "({$key})";
  },
];
