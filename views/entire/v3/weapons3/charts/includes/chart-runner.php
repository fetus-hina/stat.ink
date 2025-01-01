<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
