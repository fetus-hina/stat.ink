<?php

declare(strict_types=1);

use app\assets\BattleListAsset;
use app\assets\BattleListGroupHeaderAsset;
use app\assets\Spl2WeaponAsset;
use app\components\grid\KillRatioColumn;
use app\components\helpers\Battle as BattleHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Battle3FilterWidget;
use app\components\widgets\EmbedVideo;
use app\components\widgets\FA;
use app\components\widgets\GameModeIcon;
use app\components\widgets\Label;
use app\components\widgets\SnsWidget;
use app\components\widgets\UserMiniInfo3;
use app\components\widgets\v3\Result;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Battle3;
use app\models\User;
use yii\bootstrap\ActiveForm;
use yii\data\BaseDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var BaseDataProvider $battleDataProvider
 * @var User $user
 * @var View $this
 * @var array $summary
 */

echo implode('', [
  $this->render('pager', ['battleDataProvider' => $battleDataProvider]),
  $this->render('//includes/battles-summary', [
    'headingText' => Yii::t('app', 'Summary: Based on the current filter'),
    'summary' => $summary,
  ]),
  $this->render('buttons', ['user' => $user]),
  $this->render('list', ['battleDataProvider' => $battleDataProvider]),
  $this->render('pager', ['battleDataProvider' => $battleDataProvider]),
]) . "\n";
