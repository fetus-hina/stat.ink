<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

TwitterWebIntentsAsset::register($this);

echo implode(' ', [
  Html::a(
    '@' . Html::encode($user->loginWithTwitter->screen_name),
    vsprintf('https://twitter.com/intent/user?%s', [
      http_build_query(
        ['user_id' => $user->loginWithTwitter->twitter_id],
      ),
    ]),
  ),
  sprintf('(%s)', Html::encode($user->loginWithTwitter->name)),
  Html::a(
    implode(' ', [
      Icon::appLink(),
      Html::encode(Yii::t('app', 'Another account')),
    ]),
    ['update-login-with-twitter'],
    ['class' => 'btn btn-primary'],
  ),
  Html::a(
    implode('', [
      Icon::appUnlink(),
      Html::encode(Yii::t('app', 'Unlink account')),
    ]),
    ['clear-login-with-twitter'],
    ['class' => 'btn btn-danger'],
  ),
]);
