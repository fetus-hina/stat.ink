<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\LobbyGroup3;
use app\models\User;
use yii\db\Query;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array{lobby_id: int, lobby_group_id: int, win_unknown: int, win_knockout: int, win_time: int, lose_unknown: int, lose_knockout: int, lose_time: int, total_seconds: int}[] $stats
 */

$title = Yii::t('app', '{name}\'s Battle Stats (Winning Rate)', [
  'name' => $user->name,
]);

$this->title = implode(' | ', [Yii::$app->name, $title]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

$lobbyGroups = LobbyGroup3::find()
  ->with([
    'lobby3s' => function (Query $query): void {
      $query->orderBy(['{{%lobby3}}.[[rank]]' => SORT_ASC]);
    },
  ])
  ->andWhere(['<>', 'key', 'private'])
  ->orderBy(['rank' => SORT_ASC])
  ->all();

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9">
<?php foreach ($lobbyGroups as $lobbyGroup) { ?>
      <div class="mb-3">
        <?= $this->render('win-rate/heading-lobby-group', ['lobbyGroup' => $lobbyGroup]) . "\n" ?>
        <?= $this->render('win-rate/lobbies', [
          'lobbies' => $lobbyGroup->lobby3s,
          'lobbyGroup' => $lobbyGroup,
          'stats' => array_filter(
            $stats,
            fn (array $info): bool => $info['lobby_group_id'] === $lobbyGroup->id,
          ),
        ]) . "\n" ?>
      </div>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
