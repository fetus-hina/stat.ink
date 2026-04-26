<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;

if (!Yii::$app->request->get('recovery_keys_revoked')) {
  return;
}
?>
<div class="alert alert-danger mb-3">
  <?= Html::encode(
    Yii::t('app', 'All recovery keys have been revoked because the password was changed.'),
  ) . "\n" ?>
</div>
