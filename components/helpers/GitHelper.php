<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

class GitHelper
{
    public static function getUserName(): ?string
    {
        return static::getConfig('user.name');
    }

    public static function getUserEmail(): ?string
    {
        return static::getConfig('user.email');
    }

    public static function getConfig(string $name): ?string
    {
        return static::getLine(['config', $name]);
    }

    public static function getLine(array $params): ?string
    {
        if (!$lines = static::get($params)) {
            return null;
        }

        $line = trim((string)array_shift($lines));
        return ($line === '') ? null : $line;
    }

    public static function get(array $params): ?array
    {
        $params = array_merge(['git'], $params);
        $cmdline = '/usr/bin/env ' . implode(' ', array_map(
            fn ($k, $v) => is_int($k) ? escapeshellarg((string)$v) : sprintf('%s=%s', (string)$k, escapeshellarg($v)),
            array_keys($params),
            array_values($params),
        ));

        $lines = [];
        $status = null;
        @exec($cmdline, $lines, $status);
        return ($status === 0) ? $lines : null;
    }
}
