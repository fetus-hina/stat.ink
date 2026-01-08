<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\components\widgets\Icon;
use app\models\Map3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $battles
 * @var int $wins
 * @var string $label
 * @var string|null $shortLabel
 */

if ($battles < 1) {
  return;
}

$fmt = Yii::$app->formatter;
$errInfo = StandardError::winpct($wins, $battles);

?>
<tr>
  <th scope="row">
<?php if ($shortLabel ?? null) { ?>
    <div class="d-block d-md-none">
      <?= Html::encode($shortLabel) . "\n" ?>
    </div>
    <div class="d-none d-md-block">
      <?= Html::encode($label) . "\n" ?>
    </div>
<?php } else { ?>
    <?= Html::encode($label) . "\n" ?>
<?php } ?>
  </th>
  <td class="text-right">
    <?= $fmt->asInteger($battles) . "\n" ?>
  </td>
  <td class="text-right">
    <?= $fmt->asInteger($wins) . "\n" ?>
  </td>
  <td class="text-center">
    <?= (
      $errInfo
        ? vsprintf('%s±%s %%', [
          $fmt->asDecimal($errInfo['rate'] * 100.0, 1),
          $fmt->asDecimal($errInfo['err95ci'] * 100.0, 1),
        ])
        : Html::encode(Yii::t('app', 'Lack of data'))
    ) . "\n" ?>
  </td>
</tr>
