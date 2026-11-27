<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\assets\zxcvbn\ZxcvbnAsset;
use app\assets\zxcvbn\ZxcvbnLanguageDeAsset;
use app\assets\zxcvbn\ZxcvbnLanguageEsEsAsset;
use app\assets\zxcvbn\ZxcvbnLanguageFrAsset;
use app\assets\zxcvbn\ZxcvbnLanguageItAsset;
use app\assets\zxcvbn\ZxcvbnLanguageKoAsset;
use app\assets\zxcvbn\ZxcvbnLanguageNlBeAsset;
use app\assets\zxcvbn\ZxcvbnLanguagePtBrAsset;
use app\assets\zxcvbn\ZxcvbnLanguageRuAsset;
use app\assets\zxcvbn\ZxcvbnLanguageZhAsset;

use function explode;
use function strtolower;

final class ZxcvbnLanguage
{
    /**
     * Language packages to load in addition to the always-loaded ones (common, en and ja),
     * keyed by the language part of the application language
     */
    private const ADDITIONAL_PACKAGES = [
        'de' => [ZxcvbnLanguageDeAsset::class, 'language-de'],
        'es' => [ZxcvbnLanguageEsEsAsset::class, 'language-es-es'],
        'fr' => [ZxcvbnLanguageFrAsset::class, 'language-fr'],
        'it' => [ZxcvbnLanguageItAsset::class, 'language-it'],
        'ko' => [ZxcvbnLanguageKoAsset::class, 'language-ko'],
        'nl' => [ZxcvbnLanguageNlBeAsset::class, 'language-nl-be'],
        'pt' => [ZxcvbnLanguagePtBrAsset::class, 'language-pt-br'],
        'ru' => [ZxcvbnLanguageRuAsset::class, 'language-ru'],
        'zh' => [ZxcvbnLanguageZhAsset::class, 'language-zh'],
    ];

    /**
     * Returns the language package asset to load in addition to the always-loaded ones
     * (common, en and ja) for the given application language
     *
     * @return class-string<ZxcvbnAsset>|null
     */
    public static function getAdditionalAssetClass(string $language): ?string
    {
        return self::ADDITIONAL_PACKAGES[self::getLanguagePart($language)][0] ?? null;
    }

    /**
     * Returns the name of the package (the key of window.zxcvbnts) whose translations
     * should be used for the feedback messages
     */
    public static function getTranslationsPackageName(string $language): string
    {
        $lang = self::getLanguagePart($language);
        return self::ADDITIONAL_PACKAGES[$lang][1] ?? match ($lang) {
            'ja' => 'language-ja',
            default => 'language-en',
        };
    }

    private static function getLanguagePart(string $language): string
    {
        return strtolower(explode('-', $language, 2)[0]);
    }
}
