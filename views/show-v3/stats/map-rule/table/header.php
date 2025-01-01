<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, Rule3> $rules
 */

echo Html::tag(
  'thead',
  Html::tag(
    'tr',
    implode('', [
      Html::tag('th', ''), // TODO: legends
      implode('', array_map(
        fn (Rule3 $rule): string => $this->render('header/rule', [
          'rule' => $rule,
          'user' => $user,
        ]),
        $rules,
      )),
    ]),
  ),
);
