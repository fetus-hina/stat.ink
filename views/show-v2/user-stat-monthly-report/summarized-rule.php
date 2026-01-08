<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use statink\yii2\stages\spl2\Spl2Stage;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $maps
 * @var array $rule
 */

$this->registerCss('.text-truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}');

echo Html::tag('h3', Html::encode(Yii::t('app-rule2', $rule['name'])), ['class' => 'mt-0']) . "\n";

if (!$maps) {
  echo Html::tag(
    'div',
    Html::encode(Yii::t('app', 'No Data')),
    ['class' => 'text-muted']
  ) . "\n";
  return;
}

usort($maps, fn($a, $b) => ($b['wins'] / $b['battles']) <=> ($a['wins'] / $a['battles'])
  ?: $b['battles'] <=> $a['battles']
  ?: strcmp($a['name'], $b['name'])
);

?>
<?= $this->render('//show-v2/user-stat-monthly-report/win-pct', [
  'battles' => array_sum(array_map(fn($a) => $a['battles'], $maps)),
  'wins' => array_sum(array_map(fn($a) => $a['wins'], $maps)),
]) . "\n" ?>

<?php foreach ($maps as $map) { ?>
<div class="row mb-2">
  <div class="col-5 col-xs-5 pt-4">
    <?= Spl2Stage::img('daytime', $map['key'], [
      'title' => Yii::t('app-map2', $map['name']),
      'class' => 'auto-tooltip w-100',
    ]) . "\n" ?>
    <div class="small text-muted text-truncate">
      <?= Yii::t('app-map2', $map['name']) . "\n" ?>
    </div>
  </div>
  <div class="col-7 col-xs-7">
    <?= $this->render('//show-v2/user-stat-monthly-report/win-pct', [
      'battles' => $map['battles'],
      'wins' => $map['wins'],
    ]) . "\n" ?>
  </div>
</div>
<?php } ?>
