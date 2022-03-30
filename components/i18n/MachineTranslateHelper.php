<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\i18n;

use Yii;
use app\components\helpers\T;
use yii\i18n\PhpMessageSource;

final class MachineTranslateHelper
{
    private const BASE_DIRECTORY = '@app/messages/_deepl';

    public static function translate(
        string $category,
        string $message,
        string $language
    ): ?string {
        if (!$paths = self::getMessagePaths($category, $language)) {
            return null;
        }

        foreach ($paths as $path) {
            if (($text = self::getMessage($path, $message)) !== null) {
                return $text;
            }
        }

        return null;
    }

    private static function getMessage(string $path, string $message): ?string
    {
        static $cache = [];
        if (!isset($cache[$path])) {
            $cache[$path] = include $path;
        }

        return $cache[$path][$message] ?? null;
    }

    private static function getMessagePaths(string $category, string $language): ?array
    {
        static $cache = [];

        $cacheId = $language . '/' . $category;
        if (!isset($cache[$cacheId])) {
            $cache[$cacheId] = self::getMessagePathsImpl($category, $language);
        }

        return $cache[$cacheId];
    }

    private static function getMessagePathsImpl(string $category, string $language): ?array
    {
        $msgSource = T::is(
            PhpMessageSource::class,
            Yii::$app->i18n->getMessageSource($category),
        );
        $fileName = $msgSource->fileMap[$category] ?? str_replace('\\', '/', $category) . '.php';
        $langCandidates = [
            $language,
            substr($language, 0, 2),
        ];
        $results = [];
        foreach ($langCandidates as $lang) {
            $path = implode('/', [
                Yii::getAlias(self::BASE_DIRECTORY),
                $lang,
                $fileName,
            ]);
            if (file_exists($path) && is_file($path) && is_readable($path)) {
                $results[] = $path;
            }
        }
        return $results ?: null;
    }
}
