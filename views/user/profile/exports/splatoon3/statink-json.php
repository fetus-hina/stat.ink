<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use app\models\UserExportJson3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$json = UserExportJson3::find()
  ->andWhere(['user_id' => $user->id])
  ->limit(1)
  ->one();

echo $json && $json->last_battle_id > 0
  ? Html::a(
    implode(' ', [
      Icon::fileJson(),
      Html::encode(Yii::t('app', 'JSON (stat.ink format, gzipped)')),
    ]),
    ['download3', 'type' => 'user-json'],
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
