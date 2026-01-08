<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Rank3;

return function (?Rank3 $rank, ?int $sPlus): ?string {
  if (!$rank) {
    return null;
  }

  if ($rank->key === 's+' && $sPlus !== null) {
    return vsprintf('%s %d', [
      Yii::t('app-rank3', $rank->name),
      $sPlus,
    ]);
  }

  return Yii::t('app-rank3', $rank->name);
};
