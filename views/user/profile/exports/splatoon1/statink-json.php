<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

echo $user->isUserJsonReady
  ? Html::a(
    implode(' ', [
      Icon::fileJson(),
      Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
    ]),
    ['download', 'type' => 'user-json'],
    ['class' => 'btn btn-default btn-block text-left'],
  )
  : Html::button(
    implode(' ', [
      Icon::fileJson(),
      Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
    ]),
    [
      'class' => 'btn btn-default btn-block text-left',
      'disabled' => true,
    ],
  );
