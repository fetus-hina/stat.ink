<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use function array_merge;
use function compact;
use function max;
use function min;
use function sqrt;

final class StandardError
{
    public static function winpct(int $wins, int $battles): array|null
    {
        if ($battles < 10) {
            return null;
        }

        // ref. http://lfics81.techblog.jp/archives/2982884.html

        $rate1 = $wins / $battles;
        $rate2 = ($battles - $wins) / $battles; // 1.0 - $rate1;
        $stderr = sqrt($battles / ($battles - 1.5) * $rate1 * $rate2) / sqrt($battles);
        if ($stderr < 0.000001) {
            return null;
        }
        $err95ci = $stderr * 1.96;
        $err99ci = $stderr * 2.58;
        $min95ci = max(0.0, $rate1 - $err95ci);
        $max95ci = min(1.0, $rate1 + $err95ci);
        $min99ci = max(0.0, $rate1 - $err99ci);
        $max99ci = min(1.0, $rate1 + $err99ci);

        $significant = match (true) {
            $min99ci > 0.5 => '**',
            $max99ci < 0.5 => '**',
            $min95ci > 0.5 => '*',
            $max95ci < 0.5 => '*',
            default => '',
        };

        return array_merge(
            ['rate' => $rate1],
            compact('stderr', 'err95ci', 'err99ci', 'min95ci', 'max95ci', 'min99ci', 'max99ci', 'significant'),
        );
    }
}
