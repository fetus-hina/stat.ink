<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var callable(Rule3): string $ruleUrl
 */

$icon = fn (Rule3 $rule): string => Html::img(
  Yii::$app->assetManager->getAssetUrl(
    GameModeIconsAsset::register($this),
    sprintf('spl3/%s.png', $rule->key),
  ),
  [
    'class' => 'basic-icon',
    'draggable' => 'false',
    'style' => [
      '--icon-height' => '1em',
    ],
  ],
);

?>
<nav class="mb-1">
  <?= Html::tag(
    'ul',
    implode(
      '',
      array_map(
        fn (Rule3 $item): string => Html::tag(
          'li',
          Html::tag(
            'a',
            implode(' ', [
              $icon($item),
              Html::tag(
                'span',
                Html::encode(Yii::t('app-rule3', $item->name)),
                $item->key === $rule->key
                  ? []
                  : ['class' => 'd-none d-md-inline'],
              ),
            ]),
            $item->key !== $rule->key
              ? ['href' => $ruleUrl($item)]
              : [],
          ),
          [
            'role' => 'presentation',
            'class' => $item->key === $rule->key ? 'active': null,
          ],
        ),
        $rules,
      ),
    ),
    ['class' => 'nav nav-pills'],
  ) . "\n" ?>
</nav>
