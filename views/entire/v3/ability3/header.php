<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Ability;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Ability[] $abilities
 * @var View $this
 */

?>
<tr>
  <th class="bg-success"></th>
  <th class="text-center bg-success">
    <?= Html::tag('span', '#', [
      'class' => 'auto-tooltip',
      'title' => Yii::t('app', 'Players'),
    ]) . "\n" ?>
    </th>
<?php foreach ($abilities as $ability) { ?>
    <?= Html::tag(
      'th',
      Html::tag(
        'span',
        Icon::s3Ability($ability),
        [
          'class' => 'auto-tooltip',
          'style' => [
            'font-size' => '1.2em',
          ],
          'title' => Yii::t('app-ability3', $ability->name),
        ],
      ),
      [
        'class' => [
          'text-center',
          'vmiddle',
          $ability->primary_only ? 'bg-danger' : 'bg-success',
        ],
      ],
    ) . "\n" ?>
<?php } ?>
  <th class="bg-success"></th>
</tr>
