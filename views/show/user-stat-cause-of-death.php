<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\BattleFilterWidget;
use app\components\widgets\SnsWidget;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$this->context->layout = 'main';

$title = Yii::t('app', '{name}\'s Battle Stats (Cause of Death)', ['name' => $user->name]);
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

$total = array_reduce(
  array_map(
    function (stdClass $row): int {
      return (int)$row->count;
    },
    $list
  ),
  function (int $a, int $b): int {
    return $a + $b;
  },
  0
);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
      <aside>
        <nav class="mb-3">
          <ul class="nav nav-tabs"><?php
            $_groups = [
              ''            => Yii::t('app', 'Don\'t group'),
              'canonical'   => Yii::t('app', 'Group by reskins'),
              'main-weapon' => Yii::t('app', 'Group by main weapon'),
              'type'        => Yii::t('app', 'Group by weapon type'),
            ];
            $_selected = $group->hasErrors() ? '' : $group->level;
            echo implode('', array_map(
              function (string $key, string $text) use ($_selected, $filter, $user): string {
                return Html::tag(
                  'li',
                  Html::a(
                    Html::encode($text),
                    ['show/user-stat-cause-of-death',
                      'screen_name' => $user->screen_name,
                      'filter' => $filter->attributes,
                      'group' => ['level' => $key],
                    ]
                  ),
                  [
                    'class' => ($key == $_selected) ? 'active' : '',
                    'role' => 'presentation',
                  ]
                );
              },
              array_keys($_groups),
              array_values($_groups),
            ));
          ?></ul>
        </nav>
      </aside>
      <div class="table-responsive">
        <table class="table table-striped">
          <tbody>
<?php $rank = 0 ?>
<?php $last = null ?>
<?php if (!$list) { ?>
            <tr>
              <td><?= Html::encode(Yii::t('app', 'There are no data.')) ?></td>
            </tr>
<?php } else foreach ($list as $i => $row) { ?>
            <?= Html::beginTag('tr', [
              'class' => 'cause-of-death',
              'data' => [
                'name' => $row->name,
                'count' => (string)(int)$row->count,
              ],
            ]) . "\n" ?>
              <td class="text-right"><?php
                if ($last !== (int)$row->count) {
                  $rank = $i + 1;
                  $last = (int)$row->count;
                }
                echo Html::encode(Yii::$app->formatter->asInteger($rank));
              ?></td>
              <td><?= Html::encode($row->name) ?></td>
              <td class="text-right"><?= Html::encode(
                Yii::t('app', '{nFormatted} {n, plural, =1{time} other{times}}', [
                  'n' => (int)$row->count,
                  'nFormatted' => Yii::$app->formatter->asInteger((int)$row->count),
                ])
              ) ?></td>
              <td class="text-right"><?= Html::encode(
                Yii::$app->formatter->asPercent($row->count / $total, 3)
              ) ?></td>
            </tr>
<?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
      <?= BattleFilterWidget::widget([
        'route' => 'show/user-stat-cause-of-death',
        'screen_name' => $user->screen_name,
        'filter' => $filter,
        'action' => 'summarize',
      ]) . "\n" ?>
      <?= $this->render("//includes/user-miniinfo", ["user" => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
