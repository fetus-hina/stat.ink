<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\i18n;

use DirectoryIterator;
use Exception;
use Normalizer;
use Yii;
use app\models\Language;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;

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
    }

    private function runLanguage(string $lang, array $targetFileNames): bool
    {
        foreach ($targetFileNames as $targetFileName) {
            fwrite(STDERR, "Processing machine-translation: {$lang} / {$targetFileName}\n");

            $japanesePath = Yii::getAlias(static::BASE_MESSAGE_DIR) . '/ja/' . $targetFileName;
            $outputPath = Yii::getAlias(static::OUT_MESSAGE_DIR) . '/' . $lang . '/' .
                $targetFileName;

            if (!file_exists($japanesePath)) {
                continue;
            }

            if (!FileHelper::createDirectory(dirname($outputPath))) {
                fwrite(STDERR, "Could not create directory: " . dirname($outputPath) . "\n");
                return false;
            }

            $englishTexts = array_keys(include($japanesePath));

            // DeepL にパラメータがある文字列を渡すと悲劇を生むので、"{" を含むテキストは
            // 除外する。 "{start}" を "{開始}" とか翻訳されても困る…
            // ちゃんとやるには XML 要素に見えるように変換してやるのがいいのだと思われる…
            $englishTexts = array_filter($englishTexts, function (string $text): bool {
                return strpos($text, '{') === false;
            });
            natcasesort($englishTexts);
            $englishTexts = array_values($englishTexts);
            $localizedTexts = $this->translate($lang, $englishTexts);
            $outputContents = array_combine($englishTexts, $localizedTexts);

            fwrite(STDERR, "Writing...\n");
            $fh = fopen($outputPath, 'wt');
            fwrite($fh, "<?php\n\n");
            fwrite($fh, "/**\n");
            vfprintf($fh, " * @copyright Copyright (C) 2015-%d AIZAWA Hina\n", [
                gmdate('Y', time() + 9 * 86400), // JST
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
                    addslashes($en),
                    addslashes($localized),
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
            function (Language $lang): string {
                return $lang->lang;
            },
            Language::find()
                ->standard()
                ->andWhere(['not like', 'lang', ['en%', 'ja%'], false])
                ->orderBy(['lang' => SORT_ASC])
                ->all()
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
                $list
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
                array_slice($englishTexts, $i * 50, 50)
            ));
        }
        return $results;
    }

    private function doTranslate(string $lang, array $englishTexts): array
    {
        $resp = $this->httpPostJson(
            'https://api.deepl.com/v2/translate',
            [
                'auth_key' => Yii::$app->params['deepl'],
                'source_lang' => 'EN',
                'split_sentences' => '0',
                'tag_handling' => 'xml',
                'target_lang' => strtoupper($lang),
            ],
            implode('&', array_map(
                function (string $text): string {
                    return 'text=' . rawurlencode($text);
                },
                array_values($englishTexts)
            ))
        );
        return array_map(
            function (array $data): string {
                return Normalizer::normalize(trim($data['text']), Normalizer::FORM_C);
            },
            $resp['translations']
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
            $url .= (strpos($url, '?') === false) ? '?' : '&';
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
}
