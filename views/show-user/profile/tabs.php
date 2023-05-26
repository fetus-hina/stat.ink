<?php

declare(strict_types=1);

use app\models\User;
use yii\bootstrap\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var User $user
 * @var View $this
 * @var string $tab
 */

$tabs = [
  [
    'id' => '',
    'label' => Yii::t('app', 'Splatoon 3'),
    'active' => false,
    'content' => fn () => $this->render('//show-user/profile/tabs/splatoon3', ['user' => $user]),
  ],
  [
    'id' => '2',
    'label' => Yii::t('app', 'Splatoon 2'),
    'active' => false,
    'content' => fn() => $this->render('//show-user/profile/tabs/splatoon2', ['user' => $user]),
  ],
  [
    'id' => '1',
    'label' => Yii::t('app', 'Splatoon'),
    'active' => false,
    'content' => fn () => $this->render('//show-user/profile/tabs/splatoon', ['user' => $user]),
  ],
];

Pjax::begin([
  'id' => vsprintf('pjax-%s', [
    hash_hmac(
      'sha256',
      (string)$user->id,
      __FILE__,
    ),
  ]),
]);
echo "\n";
echo Tabs::widget([
  'items' => array_map(
    function (array $data) use ($tab, $user): array {
      if ($tab === $data['id']) {
        $data['active'] = true;
        $data['content'] = $data['content'](); // render the content
        $data['url'] = null;
      } else {
        $data['active'] = false;
        $data['content'] = '';
        $data['url'] = Url::to(
            ['show-user/profile',
                'screen_name' => $user->screen_name,
                'tab' => $data['id'] !== '' ? $data['id'] : null,
            ]
        );
      }
      unset($data['id']);
      return $data;
    },
    $tabs,
  ),
]) . "\n";
Pjax::end();
