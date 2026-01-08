<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\site;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Yii;
use cebe\markdown\GithubMarkdown as Markdown;
use stdClass;
use yii\helpers\Html;

use function array_merge;
use function call_user_func;
use function file_get_contents;
use function ltrim;
use function preg_match;
use function preg_replace;
use function strcmp;
use function strlen;
use function strnatcasecmp;
use function strtolower;
use function substr;
use function trim;
use function usort;

class LicenseAction extends SimpleAction
{
    private const CATEGORY_APP = 0;
    private const CATEGORY_COMPOSER = 1;
    private const CATEGORY_MANUAL = 1;
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
            $this->loadManualLicenses(),
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
                    ?: strnatcasecmp($aName, $bName)
                    ?: strcmp($aName, $bName);
            },
        );
        return $ret;
    }

    private function loadManualLicenses(): array
    {
        $basedir = Yii::getAlias('@app/data/licenses/');
        $ret = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basedir),
        );
        foreach ($it as $entry) {
            if (!$entry->isFile()) {
                continue;
            }

            $pathname = $entry->getPathname();
            if (substr($pathname, 0, strlen($basedir)) !== $basedir) {
                continue;
            }
            $basename = substr($pathname, strlen($basedir));

            if (strtolower(substr($basename, -3)) === '.md') {
                $ret[] = (object)[
                    'category' => static::CATEGORY_MANUAL,
                    'name' => substr($basename, 0, strlen($basename) - 3),
                    'html' => $this->loadMarkdown($entry->getPathname()),
                ];
            } else {
                $ret[] = (object)[
                    'category' => static::CATEGORY_MANUAL,
                    'name' => $basename,
                    'html' => $this->loadPlain($entry->getPathname()),
                ];
            }
        }
        return $ret;
    }

    private function loadComposerDepends(): array
    {
        $basedir = Yii::getAlias('@app/data/licenses-composer/');
        $ret = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basedir),
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
            $html = $this->loadPlain(
                $entry->getPathname(),
                fn (string $t): bool => (bool)preg_match('/copyright|licen[cs]e/i', $t),
            );
            if ($html) {
                $ret[] = (object)[
                    'category' => static::CATEGORY_COMPOSER,
                    'name' => substr($basename, 0, strlen($basename) - 12),
                    'html' => $html,
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
            new RecursiveDirectoryIterator($basedir),
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
            $html = $this->loadPlain(
                $entry->getPathname(),
                fn (string $t): bool => (bool)preg_match('/copyright|licen[cs]e/i', $t),
            );
            if ($html) {
                $ret[] = (object)[
                    'category' => static::CATEGORY_NPM,
                    'name' => substr($basename, 0, strlen($basename) - 12),
                    'html' => $html,
                ];
            }
        }

        return $ret;
    }

    private function loadPlain(string $path, ?callable $checker = null): ?string
    {
        $text = $this->loadFile($path, $checker);
        return $text !== null
            ? Html::tag('pre', Html::encode($text))
            : null;
    }

    private function loadMarkdown($path, ?callable $checker = null): ?string
    {
        $text = $this->loadFile($path, $checker);
        return $text !== null
            ? $this->mdParser->parse($text)
            : null;
    }

    private function loadFile(string $path, ?callable $checker): ?string
    {
        $text = file_get_contents($path, false);
        if ($checker && !call_user_func($checker, $text)) {
            return null;
        }
        return $text;
    }
}
