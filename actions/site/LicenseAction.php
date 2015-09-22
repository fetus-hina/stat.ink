<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\site;

use DirectoryIterator;
use Yii;
use cebe\markdown\GithubMarkdown as Markdown;
use yii\helpers\Html;

class LicenseAction extends SimpleAction
{
    public $view = 'license.tpl';
    private $mdParser;

    public function init()
    {
        $this->mdParser = new Markdown();
        $this->mdParser->html5 = true;
        return parent::init();
    }

    public function run()
    {
        $this->params['myself'] = (object)[
            'name' => Yii::$app->name,
            'html' => $this->loadPlain(Yii::getAlias('@app/LICENSE')),
        ];
        $this->params['depends'] = $this->loadDepends();
        return parent::run();
    }

    private function loadDepends()
    {
        $ret = [];
        $it = new DirectoryIterator(Yii::getAlias('@app/data/licenses'));
        foreach ($it as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }
            $basename = $entry->getBasename();
            if (strtolower(substr($basename, -3)) === '.md') {
                $ret[] = (object)[
                    'name' => substr($basename, 0, strlen($basename) - 3),
                    'html' => $this->loadMarkdown($entry->getPathname()),
                ];
            } else {
                $ret[] = (object)[
                    'name' => $basename,
                    'html' => $this->loadPlain($entry->getPathname()),
                ];
            }
        }
        usort(
            $ret,
            function ($a, $b) {
                $aName = trim(preg_replace('/[^0-9A-Za-z]+/', ' ', $a->name));
                $bName = trim(preg_replace('/[^0-9A-Za-z]+/', ' ', $b->name));
                return strnatcasecmp($aName, $bName);
            }
        );
        return $ret;
    }

    private function loadPlain($path)
    {
        return '<pre>' . Html::encode(file_get_contents($path, false)) . '</pre>';
    }

    private function loadMarkdown($path)
    {
        return $this->mdParser->parse(file_get_contents($path, false));
    }
}
