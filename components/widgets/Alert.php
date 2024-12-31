<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use yii\bootstrap\BootstrapAsset;

use function array_merge_recursive;

class Alert extends \yii\bootstrap\Alert
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
