<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\site;

use Yii;
use cebe\markdown\GithubMarkdown as Markdown;

use function file_get_contents;

class ApiDocAction extends SimpleAction
{
    public $view = 'api.tpl';

    public function run()
    {
        $markdown = file_get_contents(Yii::getAlias('@app/API.md'));
        $this->params['content'] = (new Markdown())->parse($markdown);

        return parent::run();
    }
}
