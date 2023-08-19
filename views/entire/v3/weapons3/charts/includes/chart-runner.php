<?php

declare(strict_types=1);

use app\assets\ChartJsAsset;
use app\assets\JqueryEasyChartjsAsset;
use yii\web\JqueryAsset;
use yii\web\View;

/**
 * @var View $this
 */

ChartJsAsset::register($this);
JqueryAsset::register($this);
JqueryEasyChartjsAsset::register($this);

$this->registerJs('$(".chart").easyChartJs();');
