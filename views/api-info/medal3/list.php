<?php

declare(strict_types=1);

use app\assets\Medal3Asset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\models\MedalCanonical3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var MedalCanonical3[] $medals
 * @var View $this
 * @var array[] $langs
 */

TableResponsiveForceAsset::register($this);

$asset = Medal3Asset::register($this);
$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed">
    <thead>
      <tr>
        <th></th>
<?php foreach ($langs as $lang) { ?>
        <?= Html::tag('th', Html::encode($lang->name), [
          'class' => $lang->htmlClasses,
          'lang' => $lang->lang,
        ]) . "\n" ?>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($medals as $medal) { ?>
      <tr>
        <td>
          <?= Html::img(
            $am->getAssetUrl($asset, $medal->gold ? 'gold.png' : 'silver.png'),
            [
              'class' => 'basic-icon',
              'draggable' => 'false',
            ],
          ) . "\n" ?>
        </td>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag(
          'td',
          ApiInfoName::widget([
            'name' => Yii::t('app-medal3', $medal->name, [], $lang->lang),
            'enName' => $medal->name,
            'lang' => $lang->lang,
          ]),
          [
            'class' => $lang->htmlClasses,
            'lang' => $lang->lang,
          ]
        ) . "\n" ?>
<?php } ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
