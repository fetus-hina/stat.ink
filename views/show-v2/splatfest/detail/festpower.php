<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\UserStatSplatfestAsset;
use app\models\Battle2;
use app\models\Splatfest2;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Splatfest2 $fest
 * @var User $user
 * @var View $this
 */

$bool = fn($v) => is_bool($v) ? $v : null;
$power = fn($v) => (float)$v > 0.1 ? (float)$v : null;

$query = Battle2::find()
  ->joinWith(['lobby', 'mode', 'rule'], false)
  ->andWhere([
    'battle2.user_id' => $user->id,
    'lobby2.key' => $lobby,
    'mode2.key' => 'fest',
    'rule2.key' => 'nawabari',
  ])
  ->andWhere(['BETWEEN', 'battle2.end_at',
    $fest->queryBeginTime->format(DateTime::ATOM),
    $fest->queryEndTime->format(DateTime::ATOM)
  ])
  ->orderBy([
    'battle2.end_at' => SORT_ASC,
  ]);

UserStatSplatfestAsset::register($this);

?>
<div class="mb-3">
  <h3><?= Html::encode($label) ?></h3>
  <?= Html::tag('div', '', [
    'class' => 'chart chart-festpower',
    'data' => [
      'labels' => [
        'estimateBad' => Yii::t('app', 'Their team\'s splatfest power'),
        'estimateGood' => Yii::t('app', 'My team\'s splatfest power'),
        'festPower' => Yii::t('app', 'Splatfest Power'),
        'lose' => Yii::t('app', 'Lose'),
        'win' => Yii::t('app', 'Win'),
      ],
      'terms' => [
        'exact' => [
          (int)$fest->beginTime->format('U'),
          (int)$fest->endTime->format('U'),
        ],
        'loose' => [
          (int)$fest->queryBeginTime->format('U'),
          (int)$fest->queryEndTime->format('U'),
        ],
      ],
      'values' => ArrayHelper::getColumn(
        $query->asArray()->all(),
        function (array $row) use ($bool, $power, $user): array {
          return [
            'at' => strtotime($row['end_at']),
            'bad' => $power($row['his_team_estimate_fest_power']),
            'good' => $power($row['my_team_estimate_fest_power']),
            'id' => (int)$row['id'],
            'isWin' => $bool($row['is_win']),
            'my' => $power($row['fest_power']),
            'url' => Url::to(['show-v2/battle', 'screen_name' => $user->screen_name, 'battle' => $row['id']], true),
          ];
        }
      ),
    ],
  ]) . "\n" ?>
</div>
