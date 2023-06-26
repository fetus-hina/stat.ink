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
use RuntimeException;
use Yii;

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

        if (!$generator = IntlDatePatternGenerator::create($locale)) {
            throw new RuntimeException('Failed to create IntlDatePatternGenerator');
        }

        $format = $generator->getBestPattern($skeleton);
        if (is_string($format)) {
            return $format;
        }

        throw new RuntimeException("Failed getBestPattern(\"{$skeleton}\")");
    }
}
