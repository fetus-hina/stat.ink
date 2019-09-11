<?php
declare(strict_types=1);

use app\assets\CounterAsset;
use app\assets\RpgAwesomeAsset;
use app\components\widgets\DigitalCounter;
use app\models\Battle;
use app\models\Salmon2;
use app\models\User;
use yii\helpers\Html;

RpgAwesomeAsset::register($this);

$users = (string)(User::getRoughCount() ?? '0');
$battles = (string)(Battle::getTotalRoughCount() ?? '0');
$jobs = (string)(Salmon2::getRoughCount() ?? '0');

$maxLen = max(strlen($users), strlen($battles), strlen($jobs));
$values = [
  [
    'icon' => '<span class="fas fa-fw fa-user"></span>',
    'label' => Yii::t('app-counter', 'Users'),
    'value' => $users,
    'type' => 'users',
  ],
  [
    'icon' => '<span class="ra ra-fw ra-crossed-swords"></span>',
    'label' => Yii::t('app-counter', 'Battles'),
    'value' => $battles,
    'type' => 'battles',
  ],
  [
    'icon' => '<span class="fas fa-fw fa-fish"></span>',
    'label' => Yii::t('app-counter', 'Jobs'),
    'value' => $jobs,
    'type' => 'salmon',
  ],
];
?>
<?= Html::tag(
  'p',
  implode('<br>',array_map(
    function (array $item) use ($maxLen): string {
      return sprintf(
        '%s %s: %s',
        $item['icon'],
        Html::encode($item['label']),
        DigitalCounter::widget([
          'value' => $item['value'],
          'digits' => $maxLen,
          'type' => $item['type'],
        ])
      );
    },
    $values
  )),
  ['class' => [
    'text-right',
    'mb-0',
  ]]
) . "\n" ?>
