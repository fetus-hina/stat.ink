<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function array_filter;
use function escapeshellarg;
use function fclose;
use function fwrite;
use function implode;
use function mb_convert_encoding;
use function proc_close;
use function proc_open;
use function sprintf;
use function stream_get_contents;
use function trim;
use function vsprintf;

class UserAgentHelper
{
    public static function summary(?string $userAgent, ?string $defaultValue = null): ?string
    {
        if (!$bowser = static::bowser($userAgent)) {
            return $defaultValue;
        }

        $result = array_filter([
            static::browserSummary($bowser),
            static::osSummary($bowser),
            static::platformSummary($bowser),
        ]);

        return $result ? implode(' / ', $result) : $defaultValue;
    }

    public static function bowser(?string $userAgent): ?array
    {
        if ($userAgent === null) {
            return null;
        }

        $userAgent = trim((string)mb_convert_encoding((string)$userAgent, 'ASCII', 'ASCII'));
        if ($userAgent === '') {
            return null;
        }

        $cmdline = vsprintf('/usr/bin/env %s %s 2>/dev/null', [
            escapeshellarg('node'),
            escapeshellarg(Yii::getAlias('@app/bin/bowser')),
        ]);
        $descSpec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
        ];
        $pipes = null;
        $handle = @proc_open($cmdline, $descSpec, $pipes);
        if (!$handle) {
            return null;
        }
        fwrite($pipes[0], $userAgent);
        fclose($pipes[0]);

        $json = @stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $status = @proc_close($handle);
        if ($status != 0) {
            return null;
        }

        try {
            return Json::decode($json);
        } catch (Throwable $e) {
            return null;
        }
    }

    protected static function platformSummary(array $data): ?string
    {
        switch (ArrayHelper::getValue($data, 'platform.type')) {
            case 'desktop':
                $type = Yii::t('app', 'PC');
                break;

            case 'mobile':
                $type = Yii::t('app', 'Mobile');
                break;

            case 'tablet':
                $type = Yii::t('app', 'Tablet');
                break;

            case 'tv':
                $type = Yii::t('app', 'TV');
                break;

            default:
                return null;
        }

        $name = ArrayHelper::getValue($data, 'platform.model');
        return $name
            ? sprintf('%s (%s)', $name, $type)
            : $type;
    }

    protected static function osSummary(array $data): ?string
    {
        $name = ArrayHelper::getValue($data, 'os.name');
        if (!$name) {
            return null;
        }

        $version = ArrayHelper::getValue(
            $data,
            'os.versionName',
            ArrayHelper::getValue($data, 'os.version'),
        );
        return $version
            ? ($name . ' ' . $version)
            : $name;
    }

    protected static function browserSummary(array $data): ?string
    {
        return ArrayHelper::getValue($data, 'browser.name');
    }
}
