<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\WeaponShortener;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $name
 */

$shortName = WeaponShortener::makeShorter($name);

?>
<?= Html::tag(
  'td',
  $name === $shortName
    ? ''
    : Html::encode($shortName),
  []
) ?>
