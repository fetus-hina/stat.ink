<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use LogicException;
use Yii;
use app\components\helpers\TypeHelper;
use cebe\markdown\GithubMarkdown;
use yii\base\Widget;
use yii\helpers\Html;

use function call_user_func;
use function file_exists;
use function file_get_contents;
use function hash;
use function implode;
use function is_readable;
use function ob_get_clean;
use function ob_start;
use function preg_match;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
use function strlen;
use function strtolower;
use function substr;

use const DIRECTORY_SEPARATOR;

final class MarkdownRendererWidget extends Widget
{
    public string $basedir;
    public string $filename;

    private ?string $lang = null;
    private bool $translated = false;

    public function run(): string
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            if (!$markdownPath = $this->findMarkdownPath()) {
                throw new LogicException('Markdown file not found');
            }

            return implode('', [
                $this->renderTranslationWarning(),
                $this->renderMarkdown($markdownPath),
            ]);
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    private function renderTranslationWarning(): string
    {
        if ($this->translated) {
            return '';
        }

        return Html::tag(
            'div',
            Html::tag('p', 'Sorry, this document is not translated.'),
            [
                'class' => 'alert alert-danger',
            ],
        );
    }

    private function renderMarkdown(string $markdownPath): string
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            $markdown = file_get_contents($markdownPath);
            $html = Yii::$app->cache->getOrSet(
                [__METHOD__, hash('sha256', $markdown)],
                fn (): string => $this->renderMarkdownImpl($markdown),
                30 * 86400,
            );

            return Html::tag(
                'div',
                $this->decorateBudoux($html),
                ['lang' => $this->lang],
            );
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    private function renderMarkdownImpl(string $markdown): string
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            $parser = new GithubMarkdown();
            $parser->html5 = true;
            $parser->enableNewlines = false; // true means: all new lines to <br>
            return preg_replace_callback(
                '/\{icon:([\w]+)\}/i',
                fn (array $match): string => TypeHelper::string(
                    call_user_func([Icon::class, $match[1]]),
                ),
                $parser->parse($markdown),
            );
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    private function decorateBudoux(string $html): string
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            ob_start();
            Budoux::begin(['lang' => $this->lang]);
            echo $html;
            Budoux::end();
            return ob_get_clean();
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    private function findMarkdownPath(): ?string
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            $this->translated = false;

            $match = null;
            $langs = [];
            if (preg_match('/^([a-z]+)-([a-z]+)\b/i', Yii::$app->language, $match)) {
                $langs[] = sprintf('%s-%s', strtolower($match[1]), strtolower($match[2])); // ja-jp
                $langs[] = strtolower($match[1]); // ja
            }
            $langs[] = 'en';
            $langs[] = 'ja';

            foreach ($langs as $lang) {
                $path = implode(DIRECTORY_SEPARATOR, [
                    $this->basedir,
                    str_replace('{lang}', $lang, $this->filename),
                ]);

                if (
                    file_exists($path) &&
                    is_readable($path)
                ) {
                    if (
                        $match &&
                        strtolower($match[1]) === substr($lang, 0, strlen($match[1]))
                    ) {
                        $this->translated = true;
                    }

                    $this->lang = $lang;
                    return $path;
                }
            }

            return null;
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }
}
