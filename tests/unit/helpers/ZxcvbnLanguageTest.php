<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\assets\zxcvbn\ZxcvbnLanguageDeAsset;
use app\assets\zxcvbn\ZxcvbnLanguageEsEsAsset;
use app\assets\zxcvbn\ZxcvbnLanguageKoAsset;
use app\assets\zxcvbn\ZxcvbnLanguageNlBeAsset;
use app\assets\zxcvbn\ZxcvbnLanguagePtBrAsset;
use app\assets\zxcvbn\ZxcvbnLanguageZhAsset;
use app\components\helpers\ZxcvbnLanguage;

final class ZxcvbnLanguageTest extends Unit
{
    /**
     * @dataProvider provideCases
     */
    public function testGetAdditionalAssetClass(string $language, ?string $expected): void
    {
        $this->assertSame($expected, ZxcvbnLanguage::getAdditionalAssetClass($language));
    }

    public function provideCases(): array
    {
        return [
            'de-DE' => ['de-DE', ZxcvbnLanguageDeAsset::class],
            'en-US (always loaded)' => ['en-US', null],
            'en-GB (always loaded)' => ['en-GB', null],
            'ja-JP (always loaded)' => ['ja-JP', null],
            'es-ES' => ['es-ES', ZxcvbnLanguageEsEsAsset::class],
            'es-MX uses es-es' => ['es-MX', ZxcvbnLanguageEsEsAsset::class],
            'ko-KR' => ['ko-KR', ZxcvbnLanguageKoAsset::class],
            'nl-NL uses nl-be' => ['nl-NL', ZxcvbnLanguageNlBeAsset::class],
            'pt-BR' => ['pt-BR', ZxcvbnLanguagePtBrAsset::class],
            'zh-CN' => ['zh-CN', ZxcvbnLanguageZhAsset::class],
            'zh-TW' => ['zh-TW', ZxcvbnLanguageZhAsset::class],
            'language only' => ['de', ZxcvbnLanguageDeAsset::class],
            'case insensitive' => ['DE-de', ZxcvbnLanguageDeAsset::class],
            'unsupported' => ['xx-XX', null],
            'empty' => ['', null],
        ];
    }

    /**
     * @dataProvider provideTranslationsCases
     */
    public function testGetTranslationsPackageName(string $language, string $expected): void
    {
        $this->assertSame($expected, ZxcvbnLanguage::getTranslationsPackageName($language));
    }

    public function provideTranslationsCases(): array
    {
        return [
            'de-DE' => ['de-DE', 'language-de'],
            'en-US' => ['en-US', 'language-en'],
            'en-GB' => ['en-GB', 'language-en'],
            'ja-JP' => ['ja-JP', 'language-ja'],
            'es-MX' => ['es-MX', 'language-es-es'],
            'nl-NL' => ['nl-NL', 'language-nl-be'],
            'pt-BR' => ['pt-BR', 'language-pt-br'],
            'zh-TW' => ['zh-TW', 'language-zh'],
            'unsupported falls back to en' => ['xx-XX', 'language-en'],
        ];
    }
}
