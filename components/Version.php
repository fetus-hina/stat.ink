<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
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

    public static function getFullHash(string $shortHash)
    {
        $lines = static::doGit(sprintf('git rev-parse %s -q', escapeshellarg($shortHash)));
        return $lines ? array_shift($lines) : null;
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
        $gitCommand = sprintf(
            'git log -n 1 --format=%s',
            escapeshellarg($format)
        );
        if (!$lines = static::doGit($gitCommand)) {
            return false;
        }
        return trim(array_shift($lines));
    }

    private static function doGit($gitCommand)
    {
        // FIXME: scl git19 があればそれを、無ければpathの通ったgitを使うひどい場当たりhack
        if (file_exists('/usr/bin/scl') &&
                is_executable('/usr/bin/scl') &&
                file_exists('/opt/rh/git19/enable')
        ) {
            $cmdline = sprintf(
                '/usr/bin/scl enable git19 %s',
                escapeshellarg($gitCommand)
            );
        } else {
            $cmdline = sprintf(
                '/bin/bash -c %s',
                escapeshellarg($gitCommand)
            );
        }

        $lines = $status = null;
        $line = exec($cmdline, $lines, $status);
        if ($status !== 0) {
            return false;
        }
        return $lines;
    }
}
