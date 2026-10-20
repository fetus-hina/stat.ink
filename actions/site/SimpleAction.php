<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\site;

use Override;
use yii\base\InvalidConfigException;
use yii\web\ViewAction as BaseAction;

class SimpleAction extends BaseAction
{
    public string|false $view = false;
    /**
     * @var array<string, mixed>
     */
    public array $params = [];

    #[Override]
    public function run(): string
    {
        if ($this->view === false) {
            throw new InvalidConfigException('The view is not configured.');
        }

        return $this->controller->render($this->view, $this->params);
    }
}
