<?php
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
$decorate = function (string $value) use ($maxLen): string {
    return substr(
        str_repeat('0', $maxLen) . $value,
        -$maxLen
    );
};

$values = [
  [
    'icon' => '<span class="fas fa-fw fa-user"></span>',
    'label' => Yii::t('app-counter', 'Users'),
    'value' => $decorate($users),
    'type' => 'users',
  ],
  [
    'icon' => '<span class="ra ra-fw ra-crossed-swords"></span>',
    'label' => Yii::t('app-counter', 'Battles'),
    'value' => $decorate($battles),
    'type' => 'battles',
  ],
  [
    'icon' => '<span class="fas fa-fw fa-fish"></span>',
    'label' => Yii::t('app-counter', 'Jobs'),
    'value' => $decorate($jobs),
    'type' => 'salmon',
  ],
];
?>
<p class="text-right" style="margin-bottom:0">
  <?= implode('<br>', array_map(
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
  )) . "\n" ?>
</p>
