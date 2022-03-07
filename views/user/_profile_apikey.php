<?php

declare(strict_types=1);

use app\models\User;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

?>
<p>
  <?= Html::encode(Yii::t('app', 'Please copy an API key below and paste to IkaLog, IkaRec or other apps that are compatible with {0}.', [Yii::$app->name])) . "\n" ?>
  <?= Html::encode(Yii::t('app', 'Please keep it secret.')) . "\n" ?>
</p>
<button class="btn btn-default auto-tooltip" id="apikey-button">
  <span class="fas fa-fw fa-eye"></span>
  <?= Html::encode(Yii::t('app', 'Show your API Token')) . "\n" ?>
</button>
<div id="apikey" style="display:none">
  <div class="input-group">
    <span class="input-group-addon">
      <span class="fas fa-key"></span>
    </span>
    <?= Html::tag('input', '', [
      'type' => 'text',
      'class' => 'form-control',
      'value' => $user->api_key,
      'readonly' => true,
    ]) . "\n" ?>
    <span class="input-group-btn">
      <?= Html::a(
        '<span class="fas fa-redo"></span>',
        ['regenerate-apikey'],
        [
          'id' => 'regenerate-apikey',
          'class' => 'btn btn-default auto-tooltip',
          'title' => Yii::t('app', 'Regenerate your API token'),
          'data' => [
            'confirm' => Yii::t('app', 'Are you sure you want to regenerate your API token?'),
            'method' => 'post',
          ],
        ]
      ) . "\n" ?>
    </span>
  </div>
</div>
<?php
$this->registerCss('#apikey input[type="text"]{font-family:Menlo,Monaco,Consolas,"Courier New",monospace}');
$this->registerJs(<<<'JS'
(function($){
  "use strict";
  $('#apikey input[type="text"]').focus(function(){
    $(this).select();
  });
})(jQuery);
JS
);
?>
