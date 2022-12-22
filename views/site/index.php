<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\InlineListAsset;
use app\assets\PaintballAsset;
use app\assets\ReactCounterAppAsset;
use app\assets\ReactIndexAppAsset;
use app\components\widgets\FA;
use app\components\widgets\GameVersionIcon;
use app\components\widgets\HappyNewYearWidget;
use app\components\widgets\IndexI18nButtons;
use app\components\widgets\SnsWidget;
use app\components\widgets\WashHandsWidget;
use app\components\widgets\alerts\ImportFromSplatnet;
use app\components\widgets\alerts\LanguageSupportLevelWarning;
use app\components\widgets\alerts\MaintenanceInfo;
use app\components\widgets\alerts\PleaseUseLatest;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\View;

/**
 * @var View $this
 */

assert($this->context instanceof Controller);
$this->context->layout = 'main';

PaintballAsset::register($this);

$gameMode = GameModeIconsAsset::register($this);

?>
<div class="container">
  <div class="text-right">
    <?= IndexI18nButtons::widget() . "\n" ?>
  </div>

  <div id="counter-app"></div>
<?php ReactCounterAppAsset::register($this); ?>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-8 col-lg-9">
      <h1 class="paintball" style="font-size:42px;margin-top:0">
        <?= Html::encode(Yii::$app->name) . "\n" ?>
      </h1>
      <p>
        <?= Html::encode(Yii::t('app', 'Staaaay Fresh!')) . "\n" ?>
      </p>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
<?php if (file_exists(Yii::getAlias('@app/views/includes/sponsored.php'))): ?>
        <?= $this->render('/includes/sponsored') . "\n" ?>
<?php endif; ?>
    </div>
  </div>
  <?= WashHandsWidget::widget() . "\n" ?>
  <?= HappyNewYearWidget::widget() . "\n" ?>
  <?= MaintenanceInfo::widget() . "\n" ?>
  <?= PleaseUseLatest::widget() . "\n" ?>
  <?= ImportFromSplatnet::widget() . "\n" ?>
  <?= LanguageSupportLevelWarning::widget() . "\n" ?>

<?php InlineListAsset::register($this) ?>
  <nav class="mb-3"><?= implode('', array_map(
    function (array $line): string {
      return Html::tag(
        'ul',
        implode('', array_map(
          function (string $html): string {
            return Html::tag('li', $html);
          },
          $line
        )),
        ['class' => 'inline-list mb-1']
      );
    },
    [
      [
        Yii::$app->user->isGuest
          ? Html::a(Html::encode(Yii::t('app', 'Join us')), ['user/register'])
          : Html::a(Html::encode(Yii::t('app', 'Your Battles')), ['show-user/profile',
            'screen_name' => Yii::$app->user->identity->screen_name,
          ]),
        Html::a(Html::encode(Yii::t('app', 'Getting Started')), ['site/start']),
        Html::a(Html::encode(Yii::t('app', 'FAQ')), ['site/faq']),
        Html::a(Html::encode(Yii::t('app', 'Stats: User Activity')), ['entire/users']),
      ],
      [
        GameVersionIcon::widget(['version' => 3]),
        Html::a(Html::encode(Yii::t('app', 'Knockout Rate')), ['entire/knockout3']),
      ],
      [
        vsprintf('%s %s', [
          GameVersionIcon::widget(['version' => 3]),
          Html::img(
            Yii::$app->assetManager->getAssetUrl($gameMode, 'spl3/salmon.png'),
            [
              'class' => 'basic-icon',
              'draggable' => 'false',
              'style' => ['--icon-height' => '1em'],
            ],
          ),
        ]),
        Html::a(Html::encode(Yii::t('app-salmon3', 'Random Loan Rate')), ['entire/salmon3-random-loan']),
        Html::a(Html::encode(Yii::t('app-salmon-tide2', 'Water Level')), ['entire/salmon3-tide']),
      ],
      [
        Html::a(Html::encode(Yii::t('app', 'Stats: K/D vs Win %')), ['entire/kd-win2']),
        Html::a(Html::encode(Yii::t('app', 'Stats: Weapons')), ['entire/weapons2']),
        Html::a(Html::encode(Yii::t('app', 'Stats: FestPwr diff vs Win %')), ['entire/festpower2']),
        Html::a(Html::encode(Yii::t('app-salmon2', 'Stats: Salmon Clear %')), ['entire/salmon-clear']),
        Html::a(Html::encode(Yii::t('app', 'Stats: Stages') . ' (Splatoon 1)'), ['stage/index']),
        Html::a(Html::encode(Yii::t('app', 'Download Stats')), ['download-stats/index']),
      ],
      [
        Html::a(Html::encode(Yii::t('app', 'About support for color-blindness')), ['site/color']),
        Html::a(Html::encode(Yii::t('app-privacy', 'About image sharing with the IkaLog team')), ['site/privacy']),
      ],
    ]
  )) ?></nav>
  <?= SnsWidget::widget() . "\n" ?>

  <?php ReactIndexAppAsset::register($this); ?>
  <div id="index-app"></div>
</div>
