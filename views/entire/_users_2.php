<?php
use app\models\Battle2;
use yii\db\Query;
use yii\helpers\Html;

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
<?php
$endAt = (new \DateTimeImmutable())
  ->setTimeZone(new \DateTimeZone(Yii::$app->timeZone))
  ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
$startAt = $endAt->sub(new \DateInterval('PT24H'));
$query = Battle2::find()
  ->innerJoinWith(['agent'], false)
  ->where(['and',
    ['>=', '{{battle2}}.[[created_at]]', $startAt->format(\DateTime::ATOM)],
    ['<', '{{battle2}}.[[created_at]]', $endAt->format(\DateTime::ATOM)],
  ])
  ->select([
    'name' => '{{agent}}.[[name]]',
    'battles' => 'COUNT(*)',
    'users' => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
  ])
  ->groupBy(['{{agent}}.[[name]]'])
  ->orderBy(['[[battles]]' => SORT_DESC, '[[users]]' => SORT_DESC, '[[name]]' => SORT_DESC])
  ->asArray();
?>
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
<?php foreach ($query->all() as $agent) { ?>
<?php
$versions = Battle2::find()
  ->innerJoinWith(['agent'], false)
  ->where(['and',
    ['{{agent}}.[[name]]' => $agent['name']],
    ['>=', '{{battle2}}.[[created_at]]', $startAt->format(\DateTime::ATOM)],
    ['<', '{{battle2}}.[[created_at]]', $endAt->format(\DateTime::ATOM)],
  ])
  ->select([
    'version' => 'MAX({{agent}}.[[version]])',
    'battles' => 'COUNT(*)',
    'users' => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
  ])
  ->groupBy(['{{battle2}}.[[agent_id]]'])
  ->asArray()
  ->all();
usort($versions, function (array $a, array $b) : int {
  return version_compare($b['version'], $a['version']);
});
?>
<?php if (count($versions)) { ?>
<?php foreach ($versions as $i => $version) { ?>
      <tr>
<?php if ($i === 0) { ?>
        <?= Html::tag(
          'th',
          Html::encode($agent['name']),
          ['rowspan' => count($versions), 'scope' => 'row']
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($agent['battles'])),
          ['rowspan' => count($versions), 'class' => 'text-right']
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::encode($fmt->asInteger($agent['users'])),
          ['rowspan' => count($versions), 'class' => 'text-right']
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
<?php } ?>
    </tbody>
  </table>
</div>
