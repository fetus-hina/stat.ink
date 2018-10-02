<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

namespace app\commands;

use Normalizer;
use Yii;
use Zend\Http\Client as HttpClient;
use app\components\helpers\I18n as I18nHelper;
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
        $status = $this->update($locale, 'weapon2', 'weapons', ArrayHelper::map(
            Weapon2::find()
                ->andWhere(['not', ['splatnet' => null]])
                ->orderBy(['splatnet' => SORT_ASC])
                ->asArray()
                ->all(),
            'splatnet',
            'name'
        ));
        return $status ? 0 : 1;
    }

    public function actionUpdateSubweapon(string $locale): int
    {
        if (!$enData = $this->getEnglishData('weapon_subs')) {
            return 1;
        }
        $status = $this->update($locale, 'subweapon2', 'weapon_subs', $enData);
        return $status ? 0 : 1;
    }

    public function actionUpdateSpecial(string $locale): int
    {
        if (!$enData = $this->getEnglishData('weapon_specials')) {
            return 1;
        }
        $status = $this->update($locale, 'special2', 'weapon_specials', $enData);
        return $status ? 0 : 1;
    }

    public function actionUpdateStage(string $locale): int
    {
        $status = $this->update($locale, 'map2', 'stages', ArrayHelper::map(
            Map2::find()
                ->andWhere(['not', ['splatnet' => null]])
                ->orderBy(['splatnet' => SORT_ASC])
                ->asArray()
                ->all(),
            'splatnet',
            'name'
        ));
        return $status ? 0 : 1;
    }

    private function getEnglishData(string $jsonKey): ?array
    {
        $cachePath = $this->getCacheFilePath('en');
        if (!file_exists($cachePath)) {
            fprintf(STDERR, "JSON file does not exist: %s\n", $cachePath);
            return null;
        }

        $json = Json::decode(file_get_contents($cachePath))[$jsonKey];
        $data = [];
        foreach ($json as $id => $value) {
            $data[$id] = $value['name'];
        }
        return $data;
    }

    private function update(
        string $locale,     // "ja-JP"
        string $fileName,   // "weapon2"
        string $jsonKey,    // "weapons"
        array $englishData  // [0 => "Sploosh-o-matic"]
    ): bool {
        // {{{
        if (!$shortLocale = $this->localeMap[$locale] ?? null) {
            fprintf(STDERR, "Unknown locale %s\n", $locale);
            return false;
        }

        $cachePath = $this->getCacheFilePath($shortLocale);
        if (!file_exists($cachePath)) {
            fprintf(STDERR, "JSON file does not exist: %s\n", $cachePath);
            return false;
        }

        fprintf(STDERR, "Checking translations (%s, %s)\n", $fileName, $locale);

        $filePath = Yii::getAlias('@app/messages') . "/{$shortLocale}/{$fileName}.php";
        $currentData = require($filePath);
        $splatNetData = Json::decode(file_get_contents($cachePath))[$jsonKey];
        $updated = false;
        foreach ($englishData as $splatNetId => $englishName) {
            $splatNetName = $splatNetData[$splatNetId]['name'] ?? null;
            if (!$splatNetName) {
                continue;
            }
            $splatNetName = static::normalize($splatNetName);
            $oldName = $currentData[$englishName] ?? null;
            if ($oldName !== $splatNetName) {
                fprintf(STDERR, "    %s => %s\n", $oldName ?: $englishName, $splatNetName);
                $updated = true;
                $currentData[$englishName] = $splatNetName;
            }
        }

        if (!$updated) {
            fwrite(STDERR, "  => NOT CHANGED\n");
            return true;
        }

        $php = I18nHelper::createTranslateTableCode($filePath, $currentData);
        file_put_contents($filePath, $php);
        fwrite(STDERR, "  => UPDATED.\n");

        return true;
        // }}}
    }

    private static function normalize(string $string): string
    {
        $string = Normalizer::normalize($string, Normalizer::FORM_C);
        $string = mb_convert_kana($string, 'asKV', 'UTF-8');
        $string = trim($string);
        return $string;
    }
}
