<?php

declare(strict_types=1);

use app\assets\Spl3AbilityAsset;
use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\models\Ability3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Ability3[] $abilities
 * @var View $this
 * @var array[] $langs
 */

TableResponsiveForceAsset::register($this);

$am = Yii::$app->assetManager;
$asset = Spl3AbilityAsset::register($this);

$iconClass = 'icon-' . substr(hash('sha256', __FILE__), 0, 8);

$this->registerCss(vsprintf('.%1$s{%2$s}.%1$s>img{%3$s}', [
  $iconClass,
  Html::cssStyleFromArray([
    'background' => '#333',
  ]),
  Html::cssStyleFromArray([
    'height' => '1.5em',
    'vertical-align' => 'middle',
    'width' => 'auto',
  ]),
]));

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed">
    <thead>
      <tr>
        <th></th>
        <th><code>key</code></th>
<?php foreach ($langs as $lang) { ?>
        <?= Html::tag('th', Html::encode($lang->name), [
          'class' => $lang->htmlClasses,
          'lang' => $lang->lang,
        ]) . "\n" ?>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($abilities as $ability) { ?>
      <tr>
        <?= Html::tag(
          'td',
          Html::img($am->getAssetUrl($asset, sprintf('%s.png', $ability->key))),
          ['class' => $iconClass]
        ) . "\n" ?>
        <td><code><?= Html::encode($ability->key) ?></code></td>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag(
          'td',
          ApiInfoName::widget([
            'name' => Yii::t('app-ability3', $ability->name, [], $lang->lang),
            'enName' => $ability->name,
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
