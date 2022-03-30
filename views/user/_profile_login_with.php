<?php

declare(strict_types=1);

use app\models\User;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

?>
<h2><?= Html::encode(Yii::t('app', 'Log in with other services')) ?></h2>
<table class="table table-striped">
  <tbody>
    <tr>
      <th>
        <span class="fab fa-twitter left"></span>Twitter
      </th>
      <td>
<?php if (Yii::$app->params['twitter']['read_enabled'] ?? false): ?>
<?php if ($user->loginWithTwitter): ?>
<?php TwitterWebIntentsAsset::register($this); ?>
        <?= implode(' ', [
          Html::a(
            '@' . Html::encode($user->loginWithTwitter->screen_name),
            'https://twitter.com/intent/user?' . http_build_query(['user_id' => $user->loginWithTwitter->twitter_id])
          ),
          sprintf('(%s)', Html::encode($user->loginWithTwitter->name)),
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fas fa-fw fa-link']),
              Html::encode(Yii::t('app', 'Another account')),
            ]),
            ['update-login-with-twitter'],
            ['class' => 'btn btn-primary']
          ),
          Html::a(
            implode('', [
              Html::tag('span', '', ['class' => 'fas fa-fw fa-unlink']),
              Html::encode(Yii::t('app', 'Unlink account')),
            ]),
            ['clear-login-with-twitter'],
            ['class' => 'btn btn-danger']
          ),
        ]) . "\n" ?>
<?php else: ?>
        <?= Html::encode(Yii::t('app', 'Disabled')) . "\n" ?>
        <?= Html::a(
          implode('', [
            Html::tag('span', '', ['class' => 'fas fa-fw fa-link']),
            Html::encode(Yii::t('app', 'Integrate')),
          ]),
          ['update-login-with-twitter'],
          ['class' => 'btn btn-primary']
        ) . "\n" ?>
<?php endif ?>
<?php else: ?>
        <?= Html::encode(Yii::t('app', 'Not configured.')) . "\n" ?>
<?php endif ?>
      </td>
    </tr>
  </tbody>
</table>
