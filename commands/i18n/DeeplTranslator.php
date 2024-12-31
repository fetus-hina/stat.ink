<?php

/**
 * @copyright Copyright (C) 2020-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\i18n;

use DOMDocument;
use DOMNode;
use DOMXPath;
use DateTimeImmutable;
use DateTimeZone;
use DirectoryIterator;
use Exception;
use Normalizer;
use ParagonIE\ConstantTime\Base32;
use Throwable;
use Yii;
use app\models\Language;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;

use function array_combine;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reduce;
use function array_slice;
use function array_unique;
use function array_values;
use function ceil;
use function count;
use function dirname;
use function explode;
use function fclose;
use function file_exists;
use function fopen;
use function fprintf;
use function fwrite;
use function http_build_query;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;
use function natcasesort;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function rawurlencode;
use function setlocale;
use function sprintf;
use function str_contains;
use function str_replace;
use function strcmp;
use function strnatcasecmp;
use function strpos;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;
use function usort;
use function vfprintf;
use function vsprintf;

use const LC_ALL;
use const LIBXML_COMPACT;
use const LIBXML_NOCDATA;
use const LIBXML_NONET;
use const SORT_ASC;
use const STDERR;
use const XML_ELEMENT_NODE;
use const XML_TEXT_NODE;

class DeeplTranslator extends Component
{
    private const BASE_MESSAGE_DIR = '@app/messages';
    private const OUT_MESSAGE_DIR = '@app/messages/_deepl';

    public function run(): bool
    {
        if (!Yii::$app->params['deepl']) {
            fwrite(STDERR, "params['deepl'] does not set.\n");
            return false;
        }

        $oldLocale = setlocale(LC_ALL, '0');
        try {
            setlocale(LC_ALL, 'C');

            $status = true;
            $files = $this->getTargetFiles();
            $langs = $this->getTargetLanguages();
            foreach ($langs as $lang) {
                if (!$this->runLanguage($lang, $files)) {
                    $status = false;
                }
            }
            return $status;
        } finally {
            setlocale(LC_ALL, $oldLocale);
        }
    }

    private function runLanguage(string $lang, array $targetFileNames): bool
    {
        foreach ($targetFileNames as $targetFileName) {
            fwrite(STDERR, "Processing machine-translation: {$lang} / {$targetFileName}\n");

            $japanesePath = Yii::getAlias(static::BASE_MESSAGE_DIR) . '/ja/' . $targetFileName;
            $outputPath = implode('/', [
                Yii::getAlias(static::OUT_MESSAGE_DIR),
                $lang,
                $targetFileName,
            ]);

            if (!file_exists($japanesePath)) {
                continue;
            }

            if (!FileHelper::createDirectory(dirname($outputPath))) {
                fwrite(STDERR, 'Could not create directory: ' . dirname($outputPath) . "\n");
                return false;
            }

            $englishTexts = array_filter(
                array_keys(include $japanesePath),
                fn ($text) => is_array(static::tokenizePattern($text)),
            );

            // "Salmon Run" は翻訳対象から外す
            $englishTexts = array_filter($englishTexts, fn ($text) => $text !== 'Salmon Run');

            usort($englishTexts, fn ($a, $b) => strnatcasecmp($a, $b) ?: strcmp($a, $b));
            $englishTexts = array_values($englishTexts);
            $localizedTexts = array_map(
                fn ($text) => static::xml2template($text),
                $this->translate(
                    $lang,
                    array_map(
                        // DeepL にパラメータがある文字列を渡すと悲劇を生むので、"{" を含むテキストは
                        // テンプレート部分を XML の要素に押し込める
                        // 当該部分は翻訳されないことになるが、 "{start}" が "{開始}" とかに変換される
                        // 最悪の事態は避けられるし、不自然に翻訳が適用されない箇所も減らせるハズ
                        fn ($text) => static::template2xml($text) ?? '',
                        $englishTexts,
                    ),
                ),
            );
            $outputContents = array_combine($englishTexts, $localizedTexts);
            $esc = fn ($text) => str_replace(['\\', "'"], ['\\\\', "\\'"], $text);
            $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo')); // Japan Time

            fwrite(STDERR, "Writing...\n");
            $fh = fopen($outputPath, 'wt');
            fwrite($fh, "<?php\n\n");
            fwrite($fh, "/**\n");
            vfprintf($fh, " * @copyright Copyright (C) 2015-%d AIZAWA Hina\n", [
                $now->format('Y'),
            ]);
            vfprintf($fh, " * @license %s MIT\n", [
                'https://github.com/fetus-hina/stat.ink/blob/master/LICENSE',
            ]);
            fwrite($fh, " * @author AIZAWA Hina <hina@fetus.jp>\n");
            fwrite($fh, " */\n\n");
            fwrite($fh, "declare(strict_types=1);\n\n");
            fwrite($fh, "return [\n");
            foreach ($outputContents as $en => $localized) {
                vfprintf($fh, "    '%s' => '%s',\n", [
                    $esc($en),
                    $esc($localized),
                ]);
            }
            fwrite($fh, "];\n");
            fclose($fh);
            fwrite(STDERR, "  -- Wrote!\n");
        }

        return true;
    }

    private function getTargetLanguages(): array
    {
        $statinkLanguages = array_map(
            fn (Language $lang) => $lang->lang,
            Language::find()
                ->standard()
                ->andWhere(['not like', 'lang', ['en%', 'ja%'], false])
                ->orderBy(['lang' => SORT_ASC])
                ->all(),
        );
        $deeplLanguages = $this->getDeeplSupportedLanguages();
        $results = [];
        foreach ($deeplLanguages as $lang) {
            if (in_array($lang, $statinkLanguages, true)) {
                $results[] = $lang;
            } else {
                foreach ($statinkLanguages as $lang2) {
                    if ($lang === substr($lang2, 0, 2)) {
                        $results[] = $lang;
                    }
                }
            }
        }
        return array_values(array_unique($results));
    }

    private function getDeeplSupportedLanguages(): array
    {
        static $cache = null;
        if ($cache === null) {
            $list = $this->httpGetJson('https://api.deepl.com/v2/languages', [
                'auth_key' => Yii::$app->params['deepl'],
                'type' => 'target',
            ]);
            $cache = array_filter(array_map(
                function (array $data): ?string {
                    if (!isset($data['language'])) {
                        return null;
                    }

                    $lang = $data['language'];
                    if (!is_string($lang)) {
                        return null;
                    }

                    $pos = strpos($lang, '-');
                    if ($pos === false) {
                        return strtolower($lang);
                    }

                    return vsprintf('%s-%s', [
                        strtolower(substr($lang, 0, $pos)),
                        strtoupper(substr($lang, $pos + 1)),
                    ]);
                },
                $list,
            ));
        }
        return $cache;
    }

    private function getTargetFiles(): array
    {
        $list = [];
        $it = new DirectoryIterator(Yii::getAlias(static::BASE_MESSAGE_DIR) . '/ja');
        foreach ($it as $entry) {
            if (
                $entry->isDot() ||
                $entry->isDir() ||
                strtolower((string)$entry->getExtension()) !== 'php'
            ) {
                continue;
            }

            $basename = $entry->getFilename();
            if (
                $basename === 'fest.php' ||
                preg_match('/^ability/', $basename) ||
                preg_match('/^apidoc/', $basename) ||
                preg_match('/^brand/', $basename) ||
                preg_match('/^freshness/', $basename) ||
                ($basename !== 'gearstat.php' && preg_match('/^gear/', $basename)) ||
                preg_match('/^map/', $basename) ||
                preg_match('/^rank/', $basename) ||
                preg_match('/^rule/', $basename) ||
                (
                    preg_match('/^salmon/', $basename) &&
                    (
                        $basename === 'salmon-boss2.php' ||
                        $basename === 'salmon-event2.php' ||
                        $basename === 'salmon-map2.php' ||
                        $basename === 'salmon-title2.php'
                    )
                ) ||
                preg_match('/^slack/', $basename) ||
                preg_match('/^special/', $basename) ||
                preg_match('/^subweapon/', $basename) ||
                preg_match('/^ua_vars/', $basename) ||
                preg_match('/^weapon/', $basename)
            ) {
                continue;
            }

            $list[] = $basename;
        }

        natcasesort($list);
        return array_values($list);
    }

    private function translate(string $lang, array $englishTexts): array
    {
        $results = [];
        // DeepL API supports only 50 sentences each call
        $count = (int)ceil(count($englishTexts) / 50);
        for ($i = 0; $i < $count; ++$i) {
            $results = array_merge($results, $this->doTranslate(
                $lang,
                array_slice($englishTexts, $i * 50, 50),
            ));
        }
        return $results;
    }

    private function doTranslate(string $lang, array $englishTexts): array
    {
        $resp = $this->httpPostJson(
            'https://api.deepl.com/v2/translate',
            array_merge(
                [
                    'auth_key' => Yii::$app->params['deepl'],
                    'source_lang' => 'EN',
                    'split_sentences' => '0',
                    'tag_handling' => 'xml',
                    'target_lang' => strtoupper($lang),
                ],
                in_array(strtoupper($lang), ['EN', 'EN-GB', 'EN-US', 'ES', 'JA', 'ZH'])
                    ? []
                    : ['formality' => 'more'],
            ),
            implode('&', array_map(
                fn ($text) => 'text=' . rawurlencode($text),
                array_values($englishTexts),
            )),
        );
        return array_map(
            fn ($data) => Normalizer::normalize(trim($data['text']), Normalizer::FORM_C),
            $resp['translations'],
        );
    }

    private function httpGetJson(string $url, array $params = []): array
    {
        fprintf(STDERR, "  * Requesting to %s\n", $url);
        $client = Yii::createObject([
            'class' => HttpClient::class,
            'transport' => CurlTransport::class,
            'responseConfig' => [
                'format' => HttpClient::FORMAT_JSON,
            ],
        ]);
        $resp = $client->createRequest()
            ->setMethod('get')
            ->setUrl($url)
            ->setData($params)
            ->send();
        if (!$resp->isOk) {
            throw new Exception('Failed to request ' . $url);
        }

        return $resp->getData();
    }

    private function httpPostJson(string $url, array $params = [], $contents = ''): array
    {
        fprintf(STDERR, "  * Requesting to %s\n", $url);
        $client = Yii::createObject([
            'class' => HttpClient::class,
            'transport' => CurlTransport::class,
            'responseConfig' => [
                'format' => HttpClient::FORMAT_JSON,
            ],
        ]);
        if ($params) {
            $url .= str_contains($url, '?') ? '&' : '?';
            $url .= http_build_query($params, '', '&');
        }
        $req = $client->createRequest()
            ->setMethod('post')
            ->setUrl($url);
        if (is_array($contents)) {
            $req->setData($contents);
        } else {
            $req->addHeaders(['content-type' => 'application/x-www-form-urlencoded; charset=UTF-8'])
                ->setContent($contents);
        }
        $resp = $req->send();
        if (!$resp->isOk) {
            throw new Exception('Failed to request ' . $url);
        }

        return $resp->getData();
    }

    private static function template2xml(string $text): ?string
    {
        if (!str_contains($text, '{')) {
            return $text;
        }

        // HTMLタグの中にテンプレートが存在するときに、
        // 通常の処理を行うと、テンプレート部分が XML に置換されて破壊される場合があるので
        // タグのようなものを見つけたら XML として解釈できないか試行して、地の文だけ翻訳を試みる
        // Ref. https://github.com/fetus-hina/stat.ink/issues/739
        return str_contains($text, '<')
            ? static::templateToXmlMayXml($text)
            : static::templateToXmlSimple($text);
    }

    private static function templateToXmlSimple(string $text): ?string
    {
        $tokens = static::tokenizePattern($text);
        if ($tokens === false) {
            return null;
        }

        return implode('', array_map(
            function ($token): string {
                if (is_array($token)) {
                    $parameter = '{' . implode(',', $token) . '}';
                    return sprintf(' <param data="%s"/> ', Base32::encode($parameter));
                } else {
                    return $token;
                }
            },
            $tokens,
        ));
    }

    private static function templateToXmlMayXml(string $textMayXml): ?string
    {
        try {
            $wrapped = vsprintf('%s<div id="wrap">%s</div>', [
                '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
                str_replace('<br>', '<br/>', $textMayXml),
            ]);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = true;
            $doc->recover = true;
            if ($doc->loadXML($wrapped, LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NONET)) {
                $replaced = static::processTemplateXml(
                    $doc,
                    (new DOMXPath($doc))
                        ->query('/div[@id="wrap"]', $doc)
                        ->item(0),
                );
                if ($replaced !== null) {
                    return $replaced;
                }
            }
        } catch (Throwable $e) {
        }

        return static::xml2template($textMayXml);
    }

    private static function processTemplateXml(
        DOMDocument $doc,
        DOMNode $node,
        bool $isRoot = true,
    ): ?string {
        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return null;
        }

        $startTag = Html::beginTag($node->nodeName, array_reduce(
            ArrayHelper::getColumn(
                $node->attributes,
                fn (DOMNode $attr): array => [$attr->nodeName => $attr->nodeValue],
            ),
            fn (array $acc, array $cur) => array_merge($acc, $cur),
            [],
        ));

        // process empty element
        if (isset(Html::$voidElements[strtolower($node->nodeName)])) {
            return substr($startTag, 0, -1) . ' />';
        }

        $contents = [];
        foreach ($node->childNodes as $child) {
            switch ($child->nodeType) {
                case XML_ELEMENT_NODE:
                    if (!$tmp = static::processTemplateXml($doc, $child, false)) {
                        return null;
                    }
                    $contents[] = $tmp;
                    break;

                case XML_TEXT_NODE:
                    $tmp = static::templateToXmlSimple($child->nodeValue);
                    if ($tmp === null) {
                        return null;
                    }
                    $contents[] = $tmp;
                    break;

                default:
                    return null;
            }
        }

        return $isRoot
            ? implode('', $contents)
            : vsprintf('%s%s</%s>', [
                $startTag,
                implode('', $contents),
                $node->nodeName,
            ]);
    }

    private static function xml2template(string $text): string
    {
        $text = preg_replace_callback(
            '#<param data="([2-7A-Za-z]+=*)"\s*/>#',
            fn ($match) => ' ' . Base32::decode($match[1]) . ' ',
            $text,
        );
        $text = preg_replace('/\x20{2,}/', ' ', $text);
        return trim($text);
    }

    // Copied from MessageFormatter
    // Copyright (C) Copyright (c) 2008 Yii Software LLC
    private static function tokenizePattern($pattern)
    {
        $charset = Yii::$app ? Yii::$app->charset : 'UTF-8';
        $depth = 1;
        if (($start = $pos = mb_strpos($pattern, '{', 0, $charset)) === false) {
            return [$pattern];
        }
        $tokens = [mb_substr($pattern, 0, $pos, $charset)];
        while (true) {
            $open = mb_strpos($pattern, '{', $pos + 1, $charset);
            $close = mb_strpos($pattern, '}', $pos + 1, $charset);
            if ($open === false && $close === false) {
                break;
            }
            if ($open === false) {
                $open = mb_strlen($pattern, $charset);
            }
            if ($close > $open) {
                $depth++;
                $pos = $open;
            } else {
                $depth--;
                $pos = $close;
            }
            if ($depth === 0) {
                $tokens[] = explode(
                    ',',
                    mb_substr($pattern, $start + 1, $pos - $start - 1, $charset),
                    3,
                );
                $start = $pos + 1;
                $tokens[] = mb_substr($pattern, $start, $open - $start, $charset);
                $start = $open;
            }

            if ($depth !== 0 && ($open === false || $close === false)) {
                break;
            }
        }
        if ($depth !== 0) {
            return false;
        }

        return $tokens;
    }
}
