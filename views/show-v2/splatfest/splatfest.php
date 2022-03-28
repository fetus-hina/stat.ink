<?php

declare(strict_types=1);

use app\components\helpers\Html;
use app\models\Splatfest2;
use app\models\User;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Splatfest2 $fest
 * @var User $user
 * @var View $this
 * @var stdClass|null $summary
 */

if (!$summary || !$summary->count) {
  return;
}

echo '<hr>';
echo Html::tag(
  'div',
  implode('', [
    $this->render('//show-v2/splatfest/detail/heading', ['fest' => $fest, 'user' => $user]),
    Html::tag(
      'div',
      implode('', [
        $this->render('//show-v2/splatfest/detail/summary', ['summary' => $summary]),
        ($summary->fest_power_v4_normal)
          ? $this->render('//show-v2/splatfest/detail/festpower', [
            'fest' => $fest,
            'label' => Yii::t('app', 'Splatfest Power (Normal)'),
            'lobby' => 'fest_normal',
            'user' => $user,
          ])
          : '',
        ($summary->fest_power_v4_pro)
          ? $this->render('//show-v2/splatfest/detail/festpower', [
            'fest' => $fest,
            'label' => Yii::t('app', 'Splatfest Power (Pro)'),
            'lobby' => 'standard',
            'user' => $user,
          ])
          : '',
        ($summary->fest_power_v1)
          ? $this->render('//show-v2/splatfest/detail/festpower', [
            'fest' => $fest,
            'label' => Yii::t('app', 'Splatfest Power'),
            'lobby' => ['standard', 'squad_4'],
            'user' => $user,
          ])
          : '',
      ]),
      ['class' => 'ml-4']
    ),
  ]),
  ['class' => 'mb-3', 'id' => $fest->permaID]
);
