<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3|null $rule
 * @var View $this
 */

if (!$rule) {
  echo Html::tag('h3', Html::encode(Yii::t('app', 'Unknown')));
  return;
}

$assetManager = Yii::$app->assetManager;

$imgClass = sprintf('img-%s', substr(hash('sha256', __FILE__), 0, 8));
$this->registerCss(vsprintf('.%s{%s}', [
  $imgClass,
  Html::cssStyleFromArray([
    'height' => '1em',
    'vertical-align' => 'middle',
    'width' => 'auto',
  ]),
]));

?>
<?= Html::tag(
  'h3',
  Html::tag(
    'span',
    implode('', array_filter(
      [
        $assetManager
          ? Html::img(
            $assetManager->getAssetUrl(
              $assetManager->getBundle(GameModeIconsAsset::class),
              sprintf('spl3/%s.png', $rule->key),
            ),
            [
              'class' => [
                $imgClass,
                'mr-1',
              ],
            ],
          )
          : null,
        Html::encode(Yii::t('app-rule3', $rule->short_name)),
      ],
      fn (?string $text): bool => $text !== null,
    )),
    [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app-rule3', $rule->name),
    ],
  ),
  [
    'class' => 'mt-0 mb-3 omit',
  ],
) ?>
