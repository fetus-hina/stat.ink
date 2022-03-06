<?php

/**
 * @copyright Copyright (C) 2016-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Language;
use yii\helpers\Html;
use yii\helpers\Url;

use const LC_COLLATE;

class I18n
{
    public static function languageLinkTags(): string
    {
        $controller = Yii::$app->controller;
        $request = Yii::$app->request;
        if (!$controller || !$request || !$request->isGet) {
            return '';
        }
        $params = $request->get();
        unset($params['_lang_']);

        if (!$route = $controller->route) {
            return '';
        }


        $ret = [];
        foreach (Language::find()->standard()->all() as $lang) {
            $newParams = array_merge(
                [$route, '_lang_' => $lang->lang],
                $params
            );
            $ret[] = Html::tag(
                'link',
                '',
                [
                    'rel' => 'alternate',
                    'hreflang' => $lang->languageId,
                    'href' => Url::to($newParams, true),
                ]
            );
        }
        return implode("\n", $ret) . "\n";
    }

    public static function createTranslateTableCode(string $filePath, array $data): string
    {
        $localeHandler = static::switchSystemLocale(LC_COLLATE, 'C');
        uksort($data, 'strnatcasecmp');
        unset($localeHandler);

        // The author lives in Japan!
        $now = new DateTimeImmutable(
            sprintf('@%d', $_SERVER['REQUEST_TIME']),
            new DateTimeZone('Asia/Tokyo')
        );
        $php = [
            '<?php',
            '',
            '/**',
            ' * @copyright Copyright (C) 2015-' . $now->format('Y') . ' AIZAWA Hina',
            ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT',
        ];
        foreach (static::getGitContributors($filePath) as $author) {
            $php[] = ' * @author ' . $author;
        }
        $php[] = ' */';
        $php[] = '';
        $php[] = 'declare(strict_types=1);';
        $php[] = '';
        $php[] = 'return [';
        foreach ($data as $englishName => $localName) {
            $php[] = sprintf(
                "    '%s' => '%s',",
                static::addslashes($englishName),
                static::addslashes($localName)
            );
        }
        $php[] = '];';

        return implode("\n", $php) . "\n";
    }

    private static function switchSystemLocale(int $category, string $locale): Resource
    {
        $oldLocale = setlocale($category, '0'); // get current locale
        setlocale($category, $locale);
        return new Resource(
            [$category, $oldLocale],
            function (array $oldData): void {
                call_user_func_array('setlocale', $oldData);
            }
        );
    }

    private static function addslashes(string $string): string
    {
        return str_replace(
            ['\\', "'"],
            ['\\\\', "\\'"],
            $string
        );
    }

    private static function getGitContributors(string $path): array
    {
        $cmdline = sprintf(
            '/usr/bin/env git log --pretty=%s -- %s | sort | uniq',
            escapeshellarg('%an <%ae>%n%cn <%ce>'),
            escapeshellarg($path)
        );
        $status = null;
        $lines = [];
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            throw new \Exception('Could not get contributors');
        }
        $lines[] = 'AIZAWA Hina <hina@fetus.jp>';

        $authorMap = [
            'AIZAWA Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@fetus.jp>',
            'AIZAWA, Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@fetus.jp>',
            'Unknown <wkoichi@gmail.com>' => 'Koichi Watanabe <wkoichi@gmail.com>',
        ];
        $list = array_unique(
            array_map(
                function (string $name) use ($authorMap): string {
                    $name = trim($name);
                    return $authorMap[$name] ?? $name;
                },
                $lines
            )
        );
        natcasesort($list);
        return $list;
    }
}
