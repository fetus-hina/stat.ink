<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\helpers\Html;
use yii\helpers\Json;

$fmt = Yii::$app->formatter;
?>
<?= Html::tag(
  'div',
  '',
  [
    'class' => 'graph',
    'data' => [
      'ref' => 'posts2',
      'label-battle' => Yii::t('app', 'Battles'),
      'label-user' => Yii::t('app', 'Users'),
    ],
  ]
) . "\n" ?>
<h2 id="agent-2">
  <?= Html::encode(Yii::t('app', 'User Agents in last 24 hours')) . "\n" ?>
</h2>
<?php /* Eli のスクリプトが簡単に取得できるように準API的にJSONを吐いておく */ ?>
<?= Html::tag('script', Json::encode($agents), ['type' => 'application/json', 'id' => 'agents-2-data']) . "\n" ?>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th><?= Html::encode(Yii::t('app', 'User Agent')) ?></th>
        <th><?= Html::encode(Yii::t('app', 'Battles')) ?></th>
        <th><?= Html::encode(Yii::t('app', 'Users')) ?></th>
        <th><?= Html::encode(Yii::t('app', 'Version')) ?></th>
        <th><?= Html::encode(Yii::t('app', 'Battles')) ?></th>
        <th><?= Html::encode(Yii::t('app', 'Users')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($agents['agents'] as $agent) { ?>
<?php foreach ($agent['versions'] as $i => $version) { ?>
      <tr>
<?php if ($i === 0) { ?>
<?php $rowspan = count($agent['versions']) ?>
        <?= Html::tag(
          'th',
          Html::encode($agent['name']),
          ['rowspan' => $rowspan, 'scope' => 'row']
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($agent['battles'])),
          ['rowspan' => $rowspan, 'class' => 'text-right']
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($agent['users'])),
          ['rowspan' => $rowspan, 'class' => 'text-right']
        ) . "\n" ?>
<?php } ?>
        <?= Html::tag(
          'th',
          Html::encode($version['version']),
          ['scope' => 'row']
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($version['battles'])),
          ['class' => 'text-right']
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($version['users'])),
          ['class' => 'text-right']
        ) . "\n" ?>
      </tr>
<?php } ?>
<?php } ?>
    </tbody>
  </table>
</div>
