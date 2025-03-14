<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use ParagonIE\ConstantTime\Base32;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

?>
<?= Html::tag(
  'div',
  '',
  [
    'class' => 'graph',
    'data' => [
      'ref' => 'posts1',
      'label-battle' => Yii::t('app', 'Battles'),
      'label-user' => Yii::t('app', 'Users'),
    ],
  ]
) . "\n" ?>
<?php if ($combineds) { ?>
<p>
  <?= implode(' | ', array_map(
    function (string $name) : string {
      return Html::a(
        Html::encode(mb_strimwidth($name, 0, 20, '…', 'UTF-8')),
        ['entire/combined-agent', 'b32name' => Base32::encodeUnpadded($name)],
      );
    },
    $combineds
  )) . "\n" ?>
</p>
<?php } ?>
<?php if ($agentNames) { ?>
<p>
  <?= implode(' | ', array_map(
    function (string $name) : string {
      return Html::a(
        Html::encode(mb_strimwidth($name, 0, 20, '…', 'UTF-8')),
        ['entire/agent', 'b32name' => Base32::encodeUnpadded($name)],
      );
    },
    $agentNames)) . "\n" ?>
</p>
<?php } ?>
<h2>
  <?= Html::encode(Yii::t('app', 'User Agents in last 24 hours')) . "\n" ?>
</h2>
<?= GridView::widget([
  'dataProvider' => new ArrayDataProvider([
    'allModels' => $agents,
  ]),
  'tableOptions' => [
    'class' => 'table table-striped',
  ],
  'columns' => [
    [
      'attribute' => 'battle',
      'label' => Yii::t('app', 'Battles'),
      'format' => 'integer',
      'contentOptions' => [
        'class' => 'text-right',
      ],
    ],
    [
      'attribute' => 'user',
      'label' => Yii::t('app', 'Users'),
      'format' => 'integer',
      'contentOptions' => [
        'class' => 'text-right',
      ],
    ],
    [
      'label' => Yii::t('app', 'User Agent'),
      'format' => 'raw',
      'value' => function (array $model) : string {
        return sprintf(
          '%s / %s',
          $model['agent_prod_url']
            ? Html::a(Html::encode($model['agent_name']), $model['agent_prod_url'])
            : Html::encode($model['agent_name']),
          $model['agent_rev_url']
            ? Html::a(Html::encode($model['agent_version']), $model['agent_rev_url'])
            : Html::encode($model['agent_version'])
        );
      },
    ],
  ],
]) . "\n" ?>
