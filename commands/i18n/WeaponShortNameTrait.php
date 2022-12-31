<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\i18n;

use Yii;
use app\models\Language;
use app\models\SalmonMainWeapon2;
use app\models\SalmonWeapon3;
use app\models\Weapon;
use app\models\Weapon2;
use app\models\Weapon3;
use yii\console\Controller;
use yii\helpers\Console;

trait WeaponShortNameTrait
{
    public function actionShortWeapon(): int
    {
        $locales = Language::find()
            ->standard()
            ->orderBy(['lang' => SORT_ASC])
            ->all();

        if (!$this->checkLocaleDirectories($locales)) {
            $this->stderr("[WeaponShortName] STOP\n", Console::FG_PURPLE);
            return 1;
        }

        if (!$this->createLocales($locales)) {
            $this->stderr("[WeaponShortName] STOP\n", Console::FG_PURPLE);
            return 1;
        }

        $this->stderr("[WeaponShortName] Done\n");
        return 0;
    }

    private function checkLocaleDirectories(array $locales): bool
    {
        return array_reduce(
            array_map(
                fn (Language $locale): bool => $this->checkLocaleDirectory($locale),
                $locales,
            ),
            fn (bool $old, bool $new): bool => $old && $new,
            true,
        );
    }

    private function checkLocaleDirectory(Language $locale): bool
    {
        $this->stderr('[WeaponShortName] Checking locale directory for ' . $locale->lang . "\n");

        $directory = $this->getLocaleDirectory($locale);
        if ($directory !== null) {
            return true;
        }

        $this->stderr(
            '[WeaponShortName] Directory does not exist for ' . $locale->lang . "\n",
            Console::FG_PURPLE,
        );
        $this->stderr(
            '[WeaponShortName] Please make a directory or edit $map of ' . "\n" .
            '                  app\commands\i18n\WeaponShortNameTrait::getLocaleDirectory()' . "\n",
            Console::FG_PURPLE,
        );

        return false;
    }

    private function getLocaleDirectory(Language $locale): ?string
    {
        $map = [
            'de-DE' => 'de',
            'en-US' => 'en',
            'es-ES' => 'es',
            'fr-FR' => 'fr',
            'it-IT' => 'it',
            'ja-JP' => 'ja',
            'ko-KR' => 'ko',
            'nl-NL' => 'nl',
            'ru-RU' => 'ru',
        ];

        $path = implode(DIRECTORY_SEPARATOR, [
            Yii::getAlias('@app'),
            'messages',
            $map[$locale->lang] ?? $locale->lang,
        ]);

        if (!file_exists($path)) {
            $this->stderr('[WeaponShortName] path = ' . $path . "\n", Console::FG_YELLOW);
            return null;
        }

        return $path;
    }

    private function createLocales(array $locales): bool
    {
        return array_reduce(
            array_map(
                fn (Language $locale): bool => $this->createLocale($locale),
                $locales,
            ),
            fn (bool $old, bool $new): bool => $old && $new,
            true,
        );
    }

    private function createLocale(Language $locale): bool
    {
        $path = implode(DIRECTORY_SEPARATOR, [
            $this->getLocaleDirectory($locale),
            'weapon-short.php',
        ]);

        $this->stderr('[WeaponShortName] Creating ' . $path . "\n");

        $data = [];
        if (file_exists($path)) {
            $data = require $path;
        }

        // remove empty data
        $data = array_filter(
            $data,
            fn (string $value, string $key): bool => $value !== '',
            ARRAY_FILTER_USE_BOTH,
        );

        $i18n = Yii::$app->i18n;

        // Splatoon 1
        foreach (Weapon::find()->asArray()->all() as $weapon) {
            $name = $i18n->translate('app-weapon', $weapon['name'], [], $locale->lang);
            if (!isset($data[$name])) {
                $data[$name] = '';
            }
        }

        // Splatoon 2
        foreach (Weapon2::find()->asArray()->all() as $weapon) {
            $name = $i18n->translate('app-weapon2', $weapon['name'], [], $locale->lang);
            if (!isset($data[$name])) {
                $data[$name] = '';
            }
        }

        // Splatoon 3
        foreach (Weapon3::find()->asArray()->all() as $weapon) {
            $name = $i18n->translate('app-weapon3', $weapon['name'], [], $locale->lang);
            if (!isset($data[$name])) {
                $data[$name] = '';
            }
        }

        // Splatoon 2 Salmon Run
        foreach (SalmonMainWeapon2::find()->asArray()->all() as $weapon) {
            $name = $i18n->translate('app-weapon2', $weapon['name'], [], $locale->lang);
            if (!isset($data[$name])) {
                $data[$name] = '';
            }
        }

        // Splatoon 3 Salmon Run
        foreach (SalmonWeapon3::find()->all() as $weapon) {
            $name = $i18n->translate('app-weapon3', $weapon->name, [], $locale->lang);
            if (!isset($data[$name])) {
                $data[$name] = '';
            }
        }

        uksort($data, fn (string $a, string $b): int => strcasecmp($a, $b) ?: strcmp($a, $b));

        $esc = fn (string $text): string => str_replace(["\\", "'"], ["\\\\", "\\'"], $text);

        $file = [];
        $file[] = '<?php';
        $file[] = '';
        $file[] = '/**';
        $file[] = ' * @copyright Copyright (C) 2015-' . gmdate('Y', time() + 9 * 3600) . ' AIZAWA Hina';
        $file[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getContributors($path) as $contributor) {
            $file[] = ' * @author ' . $contributor;
        }
        $file[] = ' */';
        $file[] = '';
        $file[] = 'declare(strict_types=1);';
        $file[] = '';
        $file[] = 'return [';
        foreach ($data as $k => $v) {
            $file[] = vsprintf("    '%s' => '%s',", [
                $esc($k),
                $esc($v),
            ]);
        }
        $file[] = '];';

        file_put_contents(
            $path,
            implode("\n", $file) . "\n",
        );

        return true;
    }

    protected function getContributors(string $path): array
    {
        $map = [
            '/en/' => [
                'clovervidia <clovervidia@gmail.com>',
            ],
            '/en-GB/' => [
                'clovervidia <clovervidia@gmail.com>',
            ],
        ];

        $list = $this->getGitContributors($path);
        foreach ($map as $locale => $authors) {
            if (strpos($path, $locale) !== false) {
                foreach ($authors as $author) {
                    if (!in_array($author, $list, true)) {
                        $list[] = $author;
                    }
                }
            }
        }
        natcasesort($list);
        return $list;
    }
}
