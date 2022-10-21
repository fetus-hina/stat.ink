<?php

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
