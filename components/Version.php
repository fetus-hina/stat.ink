<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components;

use Exception;
use Yii;

class Version
{
    private static $revision = null;
    private static $shortRevision = null;

    public static function getVersion()
    {
        return Yii::$app->version;
    }

    public static function getRevision()
    {
        self::fetchRevision();
        return self::$revision;
    }

    public static function getShortRevision()
    {
        self::fetchRevision();
        return self::$shortRevision;
    }

    private static function fetchRevision()
    {
        if (self::$revision !== null && self::$shortRevision !== null) {
            return;
        }
        try {
            if (!$line = self::getGitLog('%H:%h')) {
                throw new Exception();
            }
            $revisions = explode(':', $line);
            if (count($revisions) !== 2) {
                throw new Exception();
            }
            self::$revision = $revisions[0];
            self::$shortRevision = $revisions[1];
        } catch (Exception $e) {
            self::$revision = false;
            self::$shortRevision = false;
        }
    }

    private static function getGitLog($format)
    {
        $cmdline = sprintf(
            '/usr/bin/env %s log -n 1 --format=%s',
            escapeshellarg('git'),
            escapeshellarg($format)
        );
        $lines = $status = null;
        $line = exec($cmdline, $lines, $status);
        if ($status !== 0) {
            return false;
        }
        return trim($line);
    }
}
