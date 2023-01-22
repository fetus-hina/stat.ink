<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use yii\web\JqueryAsset;
use yii\web\View;

/**
 * @var View $this
 */

ChartJsAsset::register($this);
JqueryAsset::register($this);

$this->registerJs(<<<'EOF'
jQuery(".chart").each(function(){
  const elem=this;
  const config=Function('"use strict";return ('+this.getAttribute("data-chart")+")")();
  const canvas=elem.appendChild(document.createElement("canvas"));
  new window.Chart(canvas.getContext("2d"),config);
});
EOF);
