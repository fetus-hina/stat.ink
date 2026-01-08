<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Weapon3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, Weapon3> $weapons
 * @var array<string, Rule3> $rules
 * @var array<string, array<string, array>> $weaponstats
 */

foreach ($weapons as $weaponKey => $weapon) {
  $weaponStats = ArrayHelper::getValue($stats, $weaponKey);
  if (!$weaponStats) {
    continue;
  }

  echo Html::tag(
    'tr',
    implode(
      '',
      [
        $this->render(
          'cell-weapon',
          array_merge(['stats' => $weaponStats], compact('weapon', 'user')),
        ),
        implode(
          '',
          array_map(
            function (string $ruleKey, Rule3 $rule) use ($weaponStats, $user, $weapon): string {
              return $this->render('cell-data', [
                'rule' => $rule,
                'stats' => ArrayHelper::getValue($weaponStats, $ruleKey),
                'user' => $user,
                'weapon' => $weapon,
              ]);
            },
            array_keys($rules),
            array_values($rules),
          ),
        ),
      ],
    ),
  ) . "\n";
}
