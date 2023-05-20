<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\dateTimeHelper;

use IntlDatePatternGenerator;
use LogicException;
use Yii;

use function class_exists;
use function is_string;

trait FormatTrait
{
    public static function formatYM(bool $full = false, ?string $locale = null): string
    {
        return self::getFormat($locale, $full ? 'yyyy MMMM' : 'yyyy MMM');
    }

    public static function formatYMDH(?string $locale = null): string
    {
        return self::getFormat($locale, 'yyyy MMM d a h');
    }

    public static function formatDH(?string $locale = null): string
    {
        return self::getFormat($locale, 'd a h');
    }

    private static function getFormat(?string $locale, string $skeleton): string
    {
        $locale = $locale ?? Yii::$app->locale;
        if (!is_string($locale)) {
            throw new LogicException();
        }

        // PHP 8.1
        if (class_exists(IntlDatePatternGenerator::class)) {
            if ($generator = IntlDatePatternGenerator::create($locale)) {
                $format = $generator->getBestPattern($skeleton);
                if (is_string($format)) {
                    return $format;
                }
            }
        }

        // PHP 8.0 fallback
        return match ($skeleton) {
            'd a h' => match ($locale) {
                'de-DE' => 'd, h \'Uhr\' a',
                'fr-CA' => 'd h \'h\' a',
                'fr-FR' => 'd h a',
                'ja-JP', 'ja-JP@calendar=japanese' => 'd日 aK時',
                'ko-KR' => 'd일 a h시',
                'nl-NL' => 'd h a',
                'zh-CN' => 'd日 ah时',
                'zh-TW' => 'd日 ah時',
                default => 'd, h a'
            },
            'yyyy MMM' => match ($locale) {
                'ja-JP', 'zh-CN', 'zh-TW' => 'yyyy年M月',
                'ja-JP@calendar=japanese' => 'Gy年M月',
                'ko-KR' => 'yyyy년 MMM',
                'ru-RU' => 'LLL yyyy \'г\'.',
                default => 'MMM yyyy'
            },
            'yyyy MMMM' => match ($locale) {
                'es-ES', 'es-MX' => 'MMMM \'de\' yyyy',
                'ja-JP', 'zh-CN', 'zh-TW' => 'yyyy年M月',
                'ja-JP@calendar=japanese' => 'Gy年M月',
                'ko-KR' => 'yyyy년 MMMM',
                'ru-RU' => 'LLLL yyyy \'г\'.',
                default => 'MMMM yyyy'
            },
            'yyyy MMM d a h' => match ($locale) {
                'de-DE' => 'd. MMM yyyy, h \'Uhr\' a',
                'en-GB', 'es-ES', 'es-MX', 'fr-FR', 'it-IT' => 'd MMM yyyy, h a',
                'fr-CA' => 'd MMM yyyy, h \'h\' a',
                'ja-JP' => 'yyyy年M月d日 aK時',
                'ja-JP@calendar=japanese' => 'Gy年M月d日 aK時',
                'ko-KR' => 'yyyy년 MMM d일 a h시',
                'nl-NL' => 'd MMM yyyy h a',
                'ru-RU' => 'd MMM yyyy \'г\'., h a',
                'zh-CN' => 'yyyy年M月d日 ah时',
                'zh-TW' => 'yyyy年M月d日 ah時',
                default => 'MMM d, yyyy, h a'
            },
            default => throw new UnexpectedValueException('Unexpected date time skeleton'),
        };
    }
}
