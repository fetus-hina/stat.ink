<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use yii\bootstrap\Alert as BaseAlert;
use yii\bootstrap\BootstrapAsset;

class Alert extends BaseAlert
{
    public $closeButton = false;

    public function init()
    {
        BootstrapAsset::register($this->view);
        parent::init();
    }

    protected function initOptions()
    {
        parent::initOptions();
        $this->options = array_merge_recursive($this->options, [
            'role' => 'alert',
        ]);
    }
}
