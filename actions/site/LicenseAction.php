<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\site;

use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Yii;
use cebe\markdown\GithubMarkdown as Markdown;
use stdClass;
use yii\helpers\Html;

class LicenseAction extends SimpleAction
{
    private const CATEGORY_COMPOSER = 0;
    private const CATEGORY_NPM = 1;

    public $view = 'license';
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
            'category' => null,
            'name' => Yii::$app->name,
            'html' => $this->loadPlain(Yii::getAlias('@app/LICENSE')),
        ];
        $this->params['depends'] = $this->loadDepends();
        return parent::run();
    }

    private function loadDepends(): array
    {
        $ret = array_merge(
            $this->loadComposerDepends(),
            $this->loadNpmDepends(),
        );
        usort(
            $ret,
            function (stdClass $a, stdClass $b): int {
                if ($a->category !== $b->category) {
                    return $a->category <=> $b->category;
                }

                $aName = trim(preg_replace('/[^0-9A-Za-z]+/', ' ', $a->name));
                $aName2 = ltrim($aName, '@');
                $bName = trim(preg_replace('/[^0-9A-Za-z]+/', ' ', $b->name));
                $bName2 = ltrim($bName, '@');
                return strnatcasecmp($aName2, $bName2)
                    ?: strnatcasecmp($aName, $bName);
            }
        );
        return $ret;
    }

    private function loadComposerDepends(): array
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
                    'category' => static::CATEGORY_COMPOSER,
                    'name' => substr($basename, 0, strlen($basename) - 3),
                    'html' => $this->loadMarkdown($entry->getPathname()),
                ];
            } else {
                $ret[] = (object)[
                    'category' => static::CATEGORY_COMPOSER,
                    'name' => $basename,
                    'html' => $this->loadPlain($entry->getPathname()),
                ];
            }
        }
        return $ret;
    }

    private function loadNpmDepends(): array
    {
        $basedir = Yii::getAlias('@app/data/licenses-npm/');
        $ret = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basedir)
        );
        foreach ($it as $entry) {
            if (!$entry->isFile()) {
                continue;
            }

            $pathname = $entry->getPathname();
            if (substr($pathname, 0, strlen($basedir)) !== $basedir) {
                continue;
            }
            if (substr($pathname, -12) !== '-LICENSE.txt') {
                continue;
            }

            $basename = substr($pathname, strlen($basedir));
            $ret[] = (object)[
                'category' => static::CATEGORY_NPM,
                'name' => substr($basename, 0, strlen($basename) - 12),
                'html' => $this->loadPlain($entry->getPathname()),
            ];
        }

        return $ret;
    }

    private function loadPlain($path): string
    {
        return '<pre>' . Html::encode(file_get_contents($path, false)) . '</pre>';
    }

    private function loadMarkdown($path): string
    {
        return $this->mdParser->parse(file_get_contents($path, false));
    }
}
