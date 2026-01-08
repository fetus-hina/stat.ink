<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi;

interface SecurityInterface
{
    public static function oapiSecUse(array $options = []): array;

    public static function oapiSecName(): string;

    public static function oapiSecurity(): array;
}
