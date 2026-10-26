<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Override;
use yii\bootstrap\BootstrapAsset;

use function array_merge_recursive;

class Alert extends \yii\bootstrap\Alert
{
    /**
     * @var array<string, mixed>|false
     */
    public $closeButton = false;

    #[Override]
    public function init(): void
    {
        BootstrapAsset::register($this->view);
        parent::init();
    }

    #[Override]
    protected function initOptions(): void
    {
        parent::initOptions();
        $this->options = array_merge_recursive($this->options, [
            'role' => 'alert',
        ]);
    }
}
