<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\show\v3\stats\BadgeAction;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\models\Rule3;
use app\models\SalmonKing3;
use app\models\Special3;
use app\models\TricolorRole3;
use app\models\User;
use app\models\UserBadge3KingSalmonid;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Special;
use app\models\UserBadge3Tricolor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var BadgeAction::ORDER_* $order
 * @var Rule3[] $rules
 * @var SalmonKing3[] $kings
 * @var Special3[] $specials
 * @var TricolorRole3[] $roles
 * @var User $user
 * @var View $this
 * @var array<string, UserBadge3KingSalmonid> $badgeKings
 * @var array<string, UserBadge3Rule> $badgeRules
 * @var array<string, UserBadge3Special> $badgeSpecials
 * @var array<string, UserBadge3Tricolor> $badgeTricolor
 * @var array<string, int> $badgeAdjust
 * @var bool $isEdiable
 * @var bool $isEditing
 */

$permLink = Url::to(['show-v3/stats-badge', 'screen_name' => $user->screen_name], true);
$title = Yii::t('app', "{name}'s Badge Progress", [
  'name' => $user->name,
]);

$this->title = implode(' | ', [Yii::$app->name, $title]);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);
$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:image', 'content' => $user->iconUrl]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:url', 'content' => $permLink]);
if ($user->twitter != '') {
  $this->registerMetaTag(['name' => 'twitter:creator', 'content' => '@' . $user->twitter]);
}

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <p class="mb-3 text-muted small">
<?php if ($isEditing) { ?>
        <?= Html::encode(
          Yii::t('app', 'You can register (estimated) unsent values here to correct the values displayed.'),
        ) . "\n" ?>
<?php } else { ?>
        <?= Html::encode(
          Yii::t('app', 'If there are any unsubmitted data, they have not been included in this tally.'),
        ) . "\n" ?>
<?php if ($badgeAdjust) { ?>
        <br>
        <?= Html::encode(
          Yii::t('app', 'The correction value specified by the user is applied.'),
        ) . "\n" ?>
<?php } ?>
<?php } ?>
      </p>
<?php if ($isEditing) { ?>
      <p class="mb-3">
        <?= Html::a(
          implode(' ', [
            Icon::back(),
            Html::encode(Yii::t('app', 'Back')),
          ]),
          ['show-v3/stats-badge', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-default'],
        ) . "\n" ?>
      </p>
      <?= Html::beginForm(
        ['show-v3/stats-correction-badge', 'screen_name' => $user->screen_name],
        'POST',
        ['class' => 'm-0 p-0'],
      ) . "\n" ?>
<?php } else { ?>
<?php if ($isEditable) { ?>
      <p class="mb-3 text-right">
        <?= Html::a(
          implode(' ', [
            Icon::edit(),
            Html::encode(Yii::t('app', 'Correction')),
          ]),
          ['show-v3/stats-correction-badge', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-link'],
        ) . "\n" ?>
      </p>
<?php } ?>
      <nav class="mb-2">
        <ul class="nav nav-pills mb-0">
<?php foreach ([BadgeAction::ORDER_DEFAULT, BadgeAction::ORDER_NUMBER] as $itemOrder) { ?>
          <?= Html::tag(
            'li',
            Html::a(
              match ($itemOrder) {
                BadgeAction::ORDER_DEFAULT => Yii::t('app', 'Default Order'),
                BadgeAction::ORDER_NUMBER => Yii::t('app', 'Highest First'),
              },
              ['show-v3/stats-badge',
                'screen_name' => $user->screen_name,
                'order' => $itemOrder ?: null,
              ],
            ),
            [
              'class' => $order === $itemOrder ? 'active' : '',
            ],
          ) . "\n" ?>
<?php } ?>
        </ul>
      </nav>
<?php } ?>
      <?= Html::beginTag('table', [
        'class' => [
          $isEditable ? 'mb-0' : 'mb-3',
          'table',
          'table-bordered',
          'table-condensed',
          'table-striped',
        ],
      ]) . "\n" ?>
        <thead>
          <tr>
            <th class="text-center" style="width:30px"></th>
            <th class="text-center omit" style="width:4em"><?= $isEditing ? Yii::t('app', 'Progress') : '' ?></th>
<?php if ($isEditing) { ?>
            <th class="text-center omit"><?= Html::encode(Yii::t('app', 'Correction Value')) ?></th>
<?php } else { ?>
            <th class="text-center omit"><?= Html::encode(Yii::t('app', 'Progress')) ?></th>
<?php } ?>
          </tr>
        </thead>
        <tbody>
          <?= $this->render('badge/table/rules', compact(
            'badgeAdjust',
            'badgeRules',
            'badgeTricolor',
            'isEditing',
            'order',
            'roles',
            'rules',
          )) . "\n" ?>
          <?= $this->render('badge/table/specials', compact(
            'badgeAdjust',
            'badgeSpecials',
            'isEditing',
            'order',
            'specials',
          )) . "\n" ?>
          <?= $this->render('badge/table/salmon-kings', compact(
            'badgeAdjust',
            'badgeKings',
            'isEditing',
            'kings',
            'order',
          )) . "\n" ?>
          <?= $this->render('badge/table/salmon-bosses', compact(
            'badgeAdjust',
            'badgeBosses',
            'bosses',
            'isEditing',
            'order',
          )) . "\n" ?>
        </tbody>
      </table>
<?php if ($isEditing) { ?>
      <p class="mt-2 mb-3">
        <?= Html::submitButton(
          implode(' ', [
            Icon::check(),
            Yii::t('app', 'Correction'),
          ]),
          [
            'class' => 'btn btn-primary btn-block',
          ],
        ) . "\n" ?>
      </p>
      </form>
      <p class="mb-3">
        <?= Html::a(
          implode(' ', [
            Icon::back(),
            Html::encode(Yii::t('app', 'Back')),
          ]),
          ['show-v3/stats-badge', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-default'],
        ) . "\n" ?>
      </p>
<?php } elseif ($isEditable) { ?>
      <p class="mb-3 text-right">
        <?= Html::a(
          implode(' ', [
            Icon::edit(),
            Html::encode(Yii::t('app', 'Correction')),
          ]),
          ['show-v3/stats-correction-badge', 'screen_name' => $user->screen_name],
          ['class' => 'btn btn-link'],
        ) . "\n" ?>
      </p>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3">
      <?= UserMiniInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
