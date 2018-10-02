<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

namespace app\commands;

use DateTimeImmutable;
use DateTimeZone;
use Normalizer;
use Yii;
use Zend\Http\Client as HttpClient;
use app\models\Map2;
use app\models\Weapon2;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Json;

class Splatoon2InkI18nController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::setAlias('@messages', '@app/messages');
        setlocale(LC_COLLATE, 'en_US');
    }

    public function getLocaleMap(): array
    {
        return [
            'de-DE' => 'de',
            'es-ES' => 'es',
            'es-MX' => 'es-MX',
            'fr-CA' => 'fr-CA',
            'fr-FR' => 'fr',
            'it-IT' => 'it',
            'ja-JP' => 'ja',
            'nl-NL' => 'nl',
            'ru-RU' => 'ru',
        ];
    }

    public function getCacheFilePath(string $lang): string
    {
        return implode('/', [
            Yii::getAlias('@runtime'),
            'splatoon2ink-json',
            $lang . '.json',
        ]);
    }

    public function actionIndex(bool $strongUpdate = true): int
    {
        if ($this->actionDownloadAll($strongUpdate) !== 0) {
            return 1;
        }

        if ($this->actionUpdateAll() !== 0) {
            return 1;
        }

        return 0;
    }

    public function actionDownloadAll(bool $force = false): int
    {
        // {{{
        foreach ($this->localeMap as $lang => $s2inkLang) {
            $path = $this->getCacheFilePath($s2inkLang);
            if (!FileHelper::createDirectory(dirname($path))) {
                fprintf(STDERR, "Could not create output directory: %s\n", dirname($path));
                return 1;
            }

            if (!file_exists($path) || $force) {
                $status = $this->actionDownload($lang, $path);
                if ($status !== 0) {
                    return 1;
                }
            } else {
                fprintf(STDERR, "Skipping download: %s => %s\n", $lang, $s2inkLang);
            }
        }

        return 0;
        // }}}
    }

    public function actionDownload(string $locale, string $outPath): int
    {
        // {{{
        $locales = $this->localeMap;
        if (!isset($locales[$locale])) {
            fwrite(STDERR, "Unknown locale \"{$locale}\".\n");
            return 1;
        }

        fprintf(STDERR, "Downloading language data (%s) from splatoon2.ink\n", $locale);

        $url = sprintf('https://splatoon2.ink/data/locale/%s.json', $locales[$locale]);
        $client = new HttpClient($url, [
            'maxredirects' => 2,
            'strict' => 1,
            'timeout' => 15,
            'useragent' => 'statink (+https://stat.ink/)',
        ]);
        $client->setMethod('GET');
        $response = $client->send();
        if (!$response->isSuccess()) {
            fprintf(STDERR, "Fetch failed, %s\n", $response->renderStatusLine());
            return 1;
        }
        $body = $response->getBody();
        try {
            Json::decode($body);
        } catch (\Exception $e) {
            fwrite(STDERR, "JSON decode failed\n");
            return 1;
        }

        if ($outPath === '-') {
            echo $body;
            return 0;
        }

        if (!$fh = @fopen($outPath, 'wb')) {
            fwrite(STDERR, "Couldn't open output file\n");
            return 1;
        }

        fwrite($fh, $body);
        fclose($fh);
        return 0;
        // }}}
    }

    public function actionUpdateAll(): int
    {
        foreach (array_keys($this->getLocaleMap()) as $locale) {
            if ($this->actionUpdateWeapon($locale) !== 0) {
                return 1;
            }

            if ($this->actionUpdateSubweapon($locale) !== 0) {
                return 1;
            }

            if ($this->actionUpdateSpecial($locale) !== 0) {
                return 1;
            }

            if ($this->actionUpdateStage($locale) !== 0) {
                return 1;
            }
        }

        return 0;
    }

    public function actionUpdateWeapon(string $locale): int
    {
        // {{{
        if (!$shortLocale = $this->localeMap[$locale] ?? null) {
            fprintf(STDERR, "Unknown locale %s\n", $locale);
            return 1;
        }

        $cachePath = $this->getCacheFilePath($shortLocale);
        if (!file_exists($cachePath)) {
            fprintf(STDERR, "JSON file does not exist: %s\n", $cachePath);
            return 1;
        }

        fprintf(STDERR, "Checking translations (weapon, %s)\n", $locale);
        $englishData = ArrayHelper::map(
            Weapon2::find()
                ->andWhere(['not', ['splatnet' => null]])
                ->orderBy(['splatnet' => SORT_ASC])
                ->asArray()
                ->all(),
            'splatnet',
            'name'
        );

        $filePath = Yii::getAlias('@app/messages') . "/{$shortLocale}/weapon2.php";
        $currentData = require($filePath);
        $splatNetData = Json::decode(file_get_contents($cachePath))['weapons'];
        $updated = false;
        foreach ($englishData as $splatNetId => $englishName) {
            $splatNetName = $splatNetData[$splatNetId]['name'] ?? null;
            if (!$splatNetName) {
                continue;
            }
            $splatNetName = static::normalize($splatNetName);
            if (($currentData[$englishName] ?? null) !== $splatNetName) {
                fprintf(
                    STDERR,
                    "  %s => %s\n",
                    $currentData[$englishName] ?: $englishName,
                    $splatNetName
                );
                $updated = true;
                $currentData[$englishName] = $splatNetName;
            }
        }
        if (!$updated) {
            return 0;
        }

        uksort($currentData, 'strcoll');
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo'));
        $php = [];
        $php[] = '<?php';
        $php[] = '/**';
        $php[] = ' * @copyright Copyright (C) 2015-' . $now->format('Y') . ' AIZAWA Hina';
        $php[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getGitContributors($filePath) as $author) {
            $php[] = ' * @author ' . $author;
        }
        $php[] = ' */';
        $php[] = '';
        $php[] = 'return [';
        foreach ($currentData as $englishName => $localName) {
            $php[] = sprintf(
                "    '%s' => '%s',",
                static::addslashes($englishName),
                static::addslashes($localName)
            );
        }
        $php[] = '];';
        $php[] = '';

        file_put_contents($filePath, implode("\n", $php));
        return 0;
        // }}}
    }

    public function actionUpdateSubweapon(string $locale): int
    {
        // {{{
        if (!$shortLocale = $this->localeMap[$locale] ?? null) {
            fprintf(STDERR, "Unknown locale %s\n", $locale);
            return 1;
        }

        $cachePath = $this->getCacheFilePath($shortLocale);
        if (!file_exists($cachePath)) {
            fprintf(STDERR, "JSON file does not exist: %s\n", $cachePath);
            return 1;
        }

        fprintf(STDERR, "Checking translations (subweapon, %s)\n", $locale);
        $enCachePath = $this->getCacheFilePath('en');
        $json = Json::decode(file_get_contents($enCachePath))['weapon_subs'];
        $englishData = [];
        foreach ($json as $id => $value) {
            $englishData[$id] = $value['name'];
        }

        $filePath = Yii::getAlias('@app/messages') . "/{$shortLocale}/subweapon2.php";
        $currentData = require($filePath);
        $splatNetData = Json::decode(file_get_contents($cachePath))['weapon_subs'];
        $updated = false;
        foreach ($englishData as $splatNetId => $englishName) {
            $splatNetName = $splatNetData[$splatNetId]['name'] ?? null;
            if (!$splatNetName) {
                continue;
            }
            $splatNetName = static::normalize($splatNetName);
            if (($currentData[$englishName] ?? null) !== $splatNetName) {
                fprintf(
                    STDERR,
                    "  %s => %s\n",
                    $currentData[$englishName] ?: $englishName,
                    $splatNetName
                );
                $updated = true;
                $currentData[$englishName] = $splatNetName;
            }
        }
        if (!$updated) {
            return 0;
        }

        uksort($currentData, 'strcoll');
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo'));
        $php = [];
        $php[] = '<?php';
        $php[] = '/**';
        $php[] = ' * @copyright Copyright (C) 2015-' . $now->format('Y') . ' AIZAWA Hina';
        $php[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getGitContributors($filePath) as $author) {
            $php[] = ' * @author ' . $author;
        }
        $php[] = ' */';
        $php[] = '';
        $php[] = 'return [';
        foreach ($currentData as $englishName => $localName) {
            $php[] = sprintf(
                "    '%s' => '%s',",
                static::addslashes($englishName),
                static::addslashes($localName)
            );
        }
        $php[] = '];';
        $php[] = '';

        file_put_contents($filePath, implode("\n", $php));
        return 0;
        // }}}
    }

    public function actionUpdateSpecial(string $locale): int
    {
        // {{{
        if (!$shortLocale = $this->localeMap[$locale] ?? null) {
            fprintf(STDERR, "Unknown locale %s\n", $locale);
            return 1;
        }

        $cachePath = $this->getCacheFilePath($shortLocale);
        if (!file_exists($cachePath)) {
            fprintf(STDERR, "JSON file does not exist: %s\n", $cachePath);
            return 1;
        }

        fprintf(STDERR, "Checking translations (special, %s)\n", $locale);
        $enCachePath = $this->getCacheFilePath('en');
        $json = Json::decode(file_get_contents($enCachePath))['weapon_specials'];
        $englishData = [];
        foreach ($json as $id => $value) {
            $englishData[$id] = $value['name'];
        }

        $filePath = Yii::getAlias('@app/messages') . "/{$shortLocale}/special2.php";
        $currentData = require($filePath);
        $splatNetData = Json::decode(file_get_contents($cachePath))['weapon_specials'];
        $updated = false;
        foreach ($englishData as $splatNetId => $englishName) {
            $splatNetName = $splatNetData[$splatNetId]['name'] ?? null;
            if (!$splatNetName) {
                continue;
            }
            $splatNetName = static::normalize($splatNetName);
            if (($currentData[$englishName] ?? null) !== $splatNetName) {
                fprintf(
                    STDERR,
                    "  %s => %s\n",
                    $currentData[$englishName] ?? $englishName,
                    $splatNetName
                );
                $updated = true;
                $currentData[$englishName] = $splatNetName;
            }
        }
        if (!$updated) {
            return 0;
        }

        uksort($currentData, 'strcoll');
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo'));
        $php = [];
        $php[] = '<?php';
        $php[] = '/**';
        $php[] = ' * @copyright Copyright (C) 2015-' . $now->format('Y') . ' AIZAWA Hina';
        $php[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getGitContributors($filePath) as $author) {
            $php[] = ' * @author ' . $author;
        }
        $php[] = ' */';
        $php[] = '';
        $php[] = 'return [';
        foreach ($currentData as $englishName => $localName) {
            $php[] = sprintf(
                "    '%s' => '%s',",
                static::addslashes($englishName),
                static::addslashes($localName)
            );
        }
        $php[] = '];';
        $php[] = '';

        file_put_contents($filePath, implode("\n", $php));
        return 0;
        // }}}
    }

    public function actionUpdateStage(string $locale): int
    {
        // {{{
        if (!$shortLocale = $this->localeMap[$locale] ?? null) {
            fprintf(STDERR, "Unknown locale %s\n", $locale);
            return 1;
        }

        $cachePath = $this->getCacheFilePath($shortLocale);
        if (!file_exists($cachePath)) {
            fprintf(STDERR, "JSON file does not exist: %s\n", $cachePath);
            return 1;
        }

        fprintf(STDERR, "Checking translations (stage, %s)\n", $locale);
        $englishData = ArrayHelper::map(
            Map2::find()
                ->andWhere(['not', ['splatnet' => null]])
                ->orderBy(['splatnet' => SORT_ASC])
                ->asArray()
                ->all(),
            'splatnet',
            'name'
        );

        $filePath = Yii::getAlias('@app/messages') . "/{$shortLocale}/map2.php";
        $currentData = require($filePath);
        $splatNetData = Json::decode(file_get_contents($cachePath))['stages'];
        $updated = false;
        foreach ($englishData as $splatNetId => $englishName) {
            $splatNetName = $splatNetData[$splatNetId]['name'] ?? null;
            if (!$splatNetName) {
                continue;
            }
            $splatNetName = static::normalize($splatNetName);
            if (($currentData[$englishName] ?? null) !== $splatNetName) {
                fprintf(
                    STDERR,
                    "  %s => %s\n",
                    $currentData[$englishName] ?: $englishName,
                    $splatNetName
                );
                $updated = true;
                $currentData[$englishName] = $splatNetName;
            }
        }
        if (!$updated) {
            return 0;
        }

        uksort($currentData, 'strcoll');
        $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo'));
        $php = [];
        $php[] = '<?php';
        $php[] = '/**';
        $php[] = ' * @copyright Copyright (C) 2015-' . $now->format('Y') . ' AIZAWA Hina';
        $php[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getGitContributors($filePath) as $author) {
            $php[] = ' * @author ' . $author;
        }
        $php[] = ' */';
        $php[] = '';
        $php[] = 'return [';
        foreach ($currentData as $englishName => $localName) {
            $php[] = sprintf(
                "    '%s' => '%s',",
                static::addslashes($englishName),
                static::addslashes($localName)
            );
        }
        $php[] = '];';
        $php[] = '';

        file_put_contents($filePath, implode("\n", $php));
        return 0;
        // }}}
    }

    private function getGitContributors(string $path): array
    {
        // {{{
        $cmdline = sprintf(
            '/usr/bin/env git log --pretty=%s -- %s | sort | uniq',
            escapeshellarg('%an <%ae>%n%cn <%ce>'),
            escapeshellarg($path)
        );
        $status = null;
        $lines = [];
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr("Could not get contributors\n");
            exit(1);
        }
        $lines[] = 'AIZAWA Hina <hina@bouhime.com>';

        $authorMap = [
            'AIZAWA, Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@bouhime.com>',
            'Unknown <wkoichi@gmail.com>' => 'Koichi Watanabe <wkoichi@gmail.com>',
        ];
        $list = array_unique(
            array_map(
                function ($name) use ($authorMap) {
                    $name = trim($name);
                    return $authorMap[$name] ?? $name;
                },
                $lines
            )
        );
        natcasesort($list);
        return $list;
        // }}}
    }

    private static function addslashes(string $string): string
    {
        return str_replace(
            ["\\", "'"],
            ["\\\\", "\\'"],
            $string
        );
    }

    private static function normalize(string $string): string
    {
        $string = Normalizer::normalize($string, Normalizer::FORM_C);
        $string = mb_convert_kana($string, 'asKV', 'UTF-8');
        $string = trim($string);
        return $string;
    }
}
