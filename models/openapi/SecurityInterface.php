<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\openapi;

interface SecurityInterface
{
    /**
     * @param string[] $options
     * @return array<string, string[]>
     */
    public static function oapiSecUse(array $options = []): array;

    public static function oapiSecName(): string;

    /**
     * @return array<string, mixed>
     */
    public static function oapiSecurity(): array;
}
