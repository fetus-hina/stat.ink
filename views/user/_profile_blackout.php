<?php

declare(strict_types=1);

use app\assets\BlackoutHintAsset;
use app\models\User;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var string $conf
 * @var string $id
 */

BlackoutHintAsset::register($this);
?>
<p>
  <?= Html::encode(
    (function () use ($conf) : string {
      switch ($conf) {
        case User::BLACKOUT_NOT_BLACKOUT:
          return Yii::t('app', 'No black out');

        case User::BLACKOUT_NOT_PRIVATE:
          return Yii::t('app', 'Black out except private battle');

        case User::BLACKOUT_NOT_FRIEND:
          return Yii::t('app', 'Black out except private battle and teammate on squad battle (tri or quad)');

        case User::BLACKOUT_ALWAYS:
          return Yii::t('app', 'Black out other players');

        default:
          return "({$conf})";
      }
    })()
  ) . "\n" ?>
</p>
<div>
  <?= $this->render('_blackout-hint', [
    'id' => $id,
    'mode' => $mode ?? null,
  ]) . "\n" ?>
</div>
<?php $this->registerJs(sprintf(
  "updateBlackOutHint(%s,%s);",
  \json_encode((string)$conf),
  \json_encode('#' . $id)
)) ?>
