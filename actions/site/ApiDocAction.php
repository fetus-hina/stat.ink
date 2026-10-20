<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\site;

use Override;
use Yii;
use app\components\helpers\TypeHelper;
use cebe\markdown\GithubMarkdown as Markdown;

use function file_get_contents;

class ApiDocAction extends SimpleAction
{
    public string|false $view = 'api.tpl';

    #[Override]
    public function run(): string
    {
        $markdown = TypeHelper::string(file_get_contents(Yii::getAlias('@app/API.md')));
        $this->params['content'] = new Markdown()->parse($markdown);

        return parent::run();
    }
}
