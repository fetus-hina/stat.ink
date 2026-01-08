<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Lobby3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$lobbies = Lobby3::find()
  ->andWhere([
    'key' => [
      'regular',
      'bankara_challenge',
      'xmatch',
    ],
  ])
  ->orderBy(['rank' => SORT_ASC])
  ->cache(86400)
  ->all();

echo Html::tag(
  'nav',
  Html::tag(
    'ul',
    implode('', [
      implode('', array_map(
        function (Lobby3 $lobby) : string {
          return Html::tag(
            'li',
            Html::a(
              trim(
                implode(' ', [
                  Icon::s3Lobby($lobby),
                  Html::tag(
                    'span',
                    Html::encode(Yii::t('app-lobby3', $lobby->name)),
                    ['class' => 'd-none d-sm-inline'],
                  ),
                ]),
              ),
              ['entire/weapons3',
                'lobby' => $lobby->key,
                'rule' => $lobby->key === 'regular' ? 'nawabari' : 'area',
              ],
              ['role' => 'presentation']
            ),
          );
        },
        $lobbies,
      )),
      Html::tag(
        'li',
        Html::tag(
          'a',
          implode(' ', [
            Icon::s3LobbyEvent(),
            Html::tag(
              'span',
              Html::encode(Yii::t('app-lobby3', 'Challenge')),
              ['class' => 'd-none d-sm-inline'],
            ),
          ]),
        ),
        [
          'class' => 'active',
          'role' => 'presentation',
        ],
      ),
    ]),
    ['class' => 'nav nav-pills'],
  ),
  ['class' => 'mb-1'],
);
