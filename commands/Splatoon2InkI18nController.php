<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Normalizer;
use Throwable;
use Yii;
use app\components\helpers\I18n as I18nHelper;
use app\models\Gear2;
use app\models\GearType;
use app\models\Map2;
use app\models\Weapon2;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;

use const SORT_ASC;
use const STDERR;

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

    public function actionIndex(bool $strongUpdate = false): int
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
        foreach ($this->getLocaleMap() as $lang => $s2inkLang) {
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
    }

    public function actionDownload(string $locale, string $outPath): int
    {
        $locales = $this->getLocaleMap();
        if (!isset($locales[$locale])) {
            fwrite(STDERR, "Unknown locale \"{$locale}\".\n");
            return 1;
        }

        fprintf(STDERR, "Downloading language data (%s) from splatoon2.ink\n", $locale);

        $url = sprintf('https://splatoon2.ink/data/locale/%s.json', $locales[$locale]);
        $client = Yii::createObject([
            'class' => HttpClient::class,
            'transport' => CurlTransport::class,
        ]);
        $request = $client->createRequest()
            ->setOptions([
                'timeout' => 15,
                'userAgent' => 'statink (+https://stat.ink/)',
                'maxRedirects' => 5,
            ])
            ->setMethod('get')
            ->setUrl($url);
        $response = $request->send();
        if (!$response->isOk) {
            fprintf(STDERR, "Fetch failed, HTTP %d\n", $response->statusCode);
            return 1;
        }
        $body = $response->content;
        try {
            Json::decode($body);
        } catch (Throwable $e) {
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

            if ($this->actionUpdateGear($locale) !== 0) {
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

    public function actionUpdateGear(string $locale): int
    {
        $status = 0;
        $status |= $this->actionUpdateHeadgear($locale);
        $status |= $this->actionUpdateClothing($locale);
        $status |= $this->actionUpdateShoes($locale);
        return $status === 0 ? 0 : 1;
    }

    public function actionUpdateHeadgear(string $locale): int
    {
        return $this->updateGear($locale, 'headgear', 'gear.head');
    }

    public function actionUpdateClothing(string $locale): int
    {
        return $this->updateGear($locale, 'clothing', 'gear.clothes');
    }

    public function actionUpdateShoes(string $locale): int
    {
        return $this->updateGear($locale, 'shoes', 'gear.shoes');
    }

    private function updateGear(string $locale, string $typeKey, string $jsonKey): int
    {
        $type = GearType::findOne(['key' => $typeKey]);
        if (!$type) {
            fprintf(STDERR, "Unknown type: %s\n", $typeKey);
            return 1;
        }
        $status = $this->update($locale, 'gear2', $jsonKey, ArrayHelper::map(
            Gear2::find()
                ->andWhere(['and',
                    ['not', ['splatnet' => null]],
                    ['type_id' => $type->id],
                ])
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

        $json = ArrayHelper::getValue(
            Json::decode(file_get_contents($cachePath)),
            $jsonKey
        );
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
        if (!$shortLocale = $this->getLocaleMap()[$locale] ?? null) {
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
        $currentData = require $filePath;
        $splatNetData = ArrayHelper::getValue(
            Json::decode(file_get_contents($cachePath)),
            $jsonKey
        );
        $updated = false;
        foreach ($englishData as $splatNetId => $englishName) {
            $splatNetName = $splatNetData[$splatNetId]['name'] ?? null;
            if (!$splatNetName) {
                continue;
            }
            $splatNetName = self::normalize($splatNetName);
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
    }

    private static function normalize(string $string): string
    {
        $string = Normalizer::normalize($string, Normalizer::FORM_C);
        $string = mb_convert_kana($string, 'asKV', 'UTF-8');
        $string = trim($string);
        return $string;
    }
}
