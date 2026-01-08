<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\models\Language;

use function strtr;

use const SORT_ASC;

final class Translator
{
    private static $langs = null;

    public static function translateToAll(
        string $category,
        string $message,
        array $params = [],
        ?int $version = 2,
    ): array {
        if (self::$langs === null) {
            self::$langs = Language::find()
                ->standard()
                ->orderBy(['lang' => SORT_ASC])
                ->all();
        }

        $i18n = Yii::$app->i18n;
        $ret = [];
        foreach (self::$langs as $lang) {
            if (self::isVersionMatch($version, $lang)) {
                $key = strtr($lang->getLanguageId(), '-', '_');
                $ret[$key] = $i18n->translate($category, $message, $params, $lang->lang);
            }
        }

        return $ret;
    }

    private static function isVersionMatch(?int $version, Language $lang): bool
    {
        if ($version === null) {
            return true;
        }

        $langCode = $lang->lang;
        if (
            $langCode === 'de-DE' ||
            $langCode === 'en-GB' ||
            $langCode === 'en-US' ||
            $langCode === 'es-ES' ||
            $langCode === 'es-MX' ||
            $langCode === 'fr-CA' ||
            $langCode === 'fr-FR' ||
            $langCode === 'it-IT' ||
            $langCode === 'ja-JP' ||
            $langCode === 'nl-NL'
        ) {
            return true;
        }

        if (
            $version >= 2 &&
            (
                $langCode === 'ru-RU' ||
                $langCode === 'zh-CN' || // unofficial
                $langCode === 'zh-TW' // unofficial
            )
        ) {
            return true;
        }

        return $version >= 3 && $langCode === 'ko-KR';
    }
}
