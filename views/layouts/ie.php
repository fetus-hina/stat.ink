<?php

declare(strict_types=1);

use app\assets\IEWarningAsset;
use app\components\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 */

IEWarningAsset::register($this);

$this->registerJs(sprintf('jQuery(%s).ieWarning();', Json::encode('#ie-warning')));
?>
<aside id="ie-warning">
  <div class="navbar bg-danger mb-0">
    <div class="container-fluid">
      <div class="container">
        <div class="navbar-header">
          <p class="navbar-text ml-0 mr-0 p-0 w-100"><?=
            Html::encode(Yii::t('app', 'This website doesn\'t support Internet Explorer. Please use a modern browser, for example Chrome or Firefox.'))
          ?></p>
        </div>
      </div>
    </div>
  </div>
</aside>
