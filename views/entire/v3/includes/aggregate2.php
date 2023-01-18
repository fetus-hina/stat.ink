<?php

declare(strict_types=1);

use app\models\Lobby3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<string, Lobby3> $lobbies
 */

echo Html::tag(
  'p',
  Html::encode(
    Yii::t('app', 'Aggregated: {rules}', [
      'rules' => implode(', ', [
        Yii::t('app-lobby3', $lobbies['xmatch']?->name ?? ''),
        Yii::t('app-lobby3', $lobbies['regular']?->name ?? ''),
        Yii::t('app-lobby3', $lobbies['splatfest_challenge']?->name ?? ''),
      ]),
    ]),
  ),
  ['class' => 'mb-3'],
);
