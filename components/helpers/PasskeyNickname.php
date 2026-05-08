<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeInterface;
use Yii;

use function mb_substr;
use function strtolower;
use function strtr;
use function trim;

final class PasskeyNickname
{
    public const MAX_NICKNAME_LENGTH = 64;
    public const ZERO_AAGUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Builds the default nickname for a newly-registered passkey.
     *
     * - If the AAGUID is identifiable (i.e. not the all-zero placeholder) and a
     *   friendly name is available, use that name (trimmed and truncated).
     * - Otherwise, fall back to the localized "Passkey ({date})" template, with
     *   the date formatted in the user's locale's short style.
     */
    public static function buildDefault(
        string $aaguid,
        ?string $aaguidName,
        DateTimeInterface $now,
        string $dateFallbackTemplate,
    ): string {
        if (self::isKnownAaguid($aaguid) && $aaguidName !== null) {
            $name = trim($aaguidName);
            if ($name !== '') {
                return self::truncate($name);
            }
        }

        $date = (string)Yii::$app->formatter->asDate($now, 'short');
        return self::truncate(strtr($dateFallbackTemplate, ['{date}' => $date]));
    }

    public static function isKnownAaguid(string $aaguid): bool
    {
        return strtolower(trim($aaguid)) !== self::ZERO_AAGUID;
    }

    private static function truncate(string $value): string
    {
        return mb_substr($value, 0, self::MAX_NICKNAME_LENGTH, 'UTF-8');
    }
}
