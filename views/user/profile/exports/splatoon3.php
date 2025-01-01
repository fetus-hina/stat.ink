<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

echo Html::tag(
  'h2',
  vsprintf('%s (%s)', [
    Html::encode(Yii::t('app', 'Export')),
    Html::encode(Yii::t('app', 'Splatoon 3')),
  ]),
) . "\n";

echo Html::tag(
  'p',
  implode('', [
    $this->render('splatoon3/statink-json', compact('user')),
    $this->render('splatoon3/salmon-json', compact('user')),
  ]),
) . "\n";
