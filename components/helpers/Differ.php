<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use Exception;
use Yii;

use function escapeshellarg;
use function exec;
use function file_exists;
use function file_put_contents;
use function gmdate;
use function implode;
use function mkdir;
use function preg_match;
use function sprintf;
use function tempnam;
use function time;
use function unlink;

class Differ
{
    public static function diff($before, $after, $nameBefore = 'old', $nameAfter = 'new')
    {
        $fBefore = static::createTmpFile($before);
        $fAfter = static::createTmpFile($after);
        $cmdline = sprintf(
            '/usr/bin/env %s -u -d -- %s %s',
            escapeshellarg('diff'),
            escapeshellarg($fBefore->get()),
            escapeshellarg($fAfter->get()),
        );
        $status = $lines = null;
        @exec($cmdline, $lines, $status);
        if ($status != 1) {
            if ($status == 0) {
                return null;
            }
            throw new Exception('Could not create diff');
        }

        if (
            isset($lines[0])
            && preg_match('/^-{3} /', $lines[0])
            && isset($lines[1])
            && preg_match('/^\+{3} /', $lines[1])
        ) {
            $time = gmdate('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()) . '.000000000 +0000';
            $lines[0] = "--- $nameBefore\t$time";
            $lines[1] = "+++ $nameAfter\t$time";
        }
        return implode("\n", $lines);
    }

    private static function createTmpFile($data)
    {
        $directory = Yii::getAlias('@app/runtime/diff');
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0700, true)) {
                throw new Exception('Could not create temporary directory');
            }
        }
        $ret = new Resource(
            tempnam($directory, 'diff-'),
            function ($path) {
                @unlink($path);
            },
        );
        if ($ret->get() === false) {
            throw new Exception('Could not create temporary file');
        }
        if (file_put_contents($ret->get(), $data) === false) {
            throw new Exception('Could not write to temporary file');
        }
        return $ret;
    }
}
