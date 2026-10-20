<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\site;

use Override;
use Yii;
use cebe\markdown\GithubMarkdown as Markdown;

use function file_get_contents;

class ApiDocAction extends SimpleAction
{
    public string|false $view = 'api.tpl';

    #[Override]
    public function run(): string
    {
        $markdown = file_get_contents(Yii::getAlias('@app/API.md'));
        $this->params['content'] = new Markdown()->parse($markdown);

        return parent::run();
    }
}
