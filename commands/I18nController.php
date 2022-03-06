<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DirectoryIterator;
use Iterator;
use Yii;
use app\models\Language;
use yii\console\Controller;

use const LC_COLLATE;

class I18nController extends Controller
{
    use i18n\GearNameTrait;
    use i18n\WeaponShortNameTrait;

    public function init()
    {
        parent::init();
        Yii::setAlias('@messages', '@app/messages');
    }

    public function actionMessages(bool $strongUpdate = false): int
    {
        $status = 0;
        $locales = Language::find()
            ->standard()
            ->andWhere(['not', ['lang' => ['ja-JP', 'en-US']]])
            ->all();
        $status |= $this->actionJapaneseGear2();
        foreach ($locales as $locale) {
            $status |= $this->actionMessage($locale->lang);
        }
        $status |= $this->kickSupportActions($strongUpdate);
        $status |= $this->actionShortWeapon();

        return $status ? 1 : 0;
    }

    public function actionMessage(string $locale): int
    {
        if (!preg_match('/^[a-z]{2}-[A-Z]{2}$/', $locale)) {
            // Note: locale may have 3 characters part, but we currently unsupported yet
            // (They used for minor languages/regions)
            $this->stderr("Invalid or unsupported locale: $locale\n");
            return 1;
        }

        $localeMap = [
            'de-DE' => 'de',
            'en-US' => 'en',
            'es-ES' => 'es',
            'fr-FR' => 'fr',
            'it-IT' => 'it',
            'nl-NL' => 'nl',
            'ru-RU' => 'ru',
        ];
        $locale = $localeMap[$locale] ?? $locale;

        $status = true;
        foreach ($this->findJapaneseFiles() as $fileName) {
            $this->stderr("Processing {$fileName} of $locale ...\n");
            $inPath = Yii::getAlias('@messages/ja') . '/' . $fileName;
            $outPath = Yii::getAlias('@messages') . '/' . $locale . '/' . $fileName;
            $status &= $this->createTranslateFile($inPath, $outPath);
        }
        return $status ? 0 : 1;
    }

    private function findJapaneseFiles(): Iterator
    {
        $it = new DirectoryIterator(Yii::getAlias('@messages/ja'));
        foreach ($it as $item) {
            if ($item->isFile() && !$item->isDot() && strtolower($item->getExtension()) === 'php') {
                // skip weapon-*** files because it includes by weapon.php
                // skip gear-*** files because it includes by gear.php
                if (
                    !preg_match('/^weapon-\w+\.php$/', $item->getBasename()) &&
                    !preg_match('/^gear-\w+\.php$/', $item->getBasename())
                ) {
                    yield $item->getBasename();
                }
            }
        }
    }

    private function createTranslateFile(string $inPath, string $outPath): bool
    {
        if (!file_exists(dirname($outPath))) {
            mkdir(dirname($outPath), 0755, true);
        }

        $changed = false;
        $inData = include $inPath;
        $current = file_exists($outPath) ? include($outPath) : [];
        foreach (array_keys($inData) as $enText) {
            if (!isset($current[$enText])) {
                $current[$enText] = '';
                $changed = true;
            }
        }
        $deleteKeys = array_diff(array_keys($current), array_keys($inData));
        foreach ($deleteKeys as $k) {
            unset($current[$k]);
            $changed = true;
        }

        if (!$changed && count($current) > 0) {
            $this->stderr("  => SKIP\n");
            return true;
        }

        setlocale(LC_COLLATE, 'C');
        uksort($current, fn (string $a, string $b): int => strnatcasecmp($a, $b) ?: strcmp($a, $b));

        $esc = fn (string $text): string => str_replace(['\\', "'"], ['\\\\', "\\'"], $text);

        $file = [];
        $file[] = '<?php';
        $file[] = '';
        $file[] = '/**';
        $file[] = ' * @copyright Copyright (C) 2015-' . gmdate('Y', time() + 9 * 3600) . ' AIZAWA Hina';
        $file[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getGitContributors($outPath) as $contributor) {
            $file[] = ' * @author ' . $contributor;
        }
        $file[] = ' */';
        $file[] = '';
        $file[] = 'declare(strict_types=1);';
        $file[] = '';
        $file[] = 'return [';
        foreach ($current as $key => $value) {
            $file[] = sprintf("    '%s' => '%s',", $esc($key), $esc($value));
        }
        $file[] = '];';
        file_put_contents($outPath, implode("\n", $file) . "\n");
        $this->stderr("  => SAVED!\n");
        return true;
    }

    private function kickSupportActions(bool $strongUpdate): int
    {
        $cmdline = sprintf(
            '/usr/bin/env %s/yii splatoon2-ink-i18n/index %d',
            Yii::getAlias('@app'),
            $strongUpdate ? 1 : 0
        );
        passthru($cmdline, $status1);

        $cmdline = sprintf(
            '/usr/bin/env %s/yii api2-markdown',
            Yii::getAlias('@app')
        );
        passthru($cmdline, $status2);

        return $status1 | $status2;
    }

    private function getGitContributors(string $path): array
    {
        // {{{
        $cmdline = sprintf(
            '/usr/bin/env git log --pretty=%s -- %s | sort | uniq',
            escapeshellarg('%an <%ae>%n%cn <%ce>'),
            escapeshellarg($path)
        );
        $status = null;
        $lines = [];
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr("Could not get contributors\n");
            exit(1);
        }
        $lines[] = 'AIZAWA Hina <hina@fetus.jp>';

        if ($this->isGosinInvolved($path)) {
            $lines[] = 'Gosin <canling0@gmail.com>';
        }

        $authorMap = [
            'AIZAWA Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@fetus.jp>',
            'AIZAWA, Hina <hina@bouhime.com>' => 'AIZAWA Hina <hina@fetus.jp>',
            'Unknown <wkoichi@gmail.com>' => 'Koichi Watanabe <wkoichi@gmail.com>',
        ];
        $list = array_unique(
            array_map(
                function ($name) use ($authorMap) {
                    $name = trim($name);
                    return $authorMap[$name] ?? $name;
                },
                $lines
            )
        );
        natcasesort($list);
        return $list;
        // }}}
    }

    private function isGosinInvolved(string $path): bool
    {
        // {{{
        $appPath = Yii::getAlias('@app/');
        if (substr($path, 0, strlen($appPath)) === $appPath) {
            $localPath = substr($path, strlen($appPath));
            if (
                $localPath === 'messages/zh-TW/ability.php' ||
                $localPath === 'messages/zh-TW/ability2.php' ||
                $localPath === 'messages/zh-TW/alert.php' ||
                $localPath === 'messages/zh-TW/apidoc1.php' ||
                $localPath === 'messages/zh-TW/apidoc2.php' ||
                $localPath === 'messages/zh-TW/app.php' ||
                $localPath === 'messages/zh-TW/brand.php' ||
                $localPath === 'messages/zh-TW/brand2.php' ||
                $localPath === 'messages/zh-TW/cookie.php' ||
                $localPath === 'messages/zh-TW/counter.php' ||
                $localPath === 'messages/zh-TW/death.php' ||
                $localPath === 'messages/zh-TW/death2.php' ||
                $localPath === 'messages/zh-TW/email.php' ||
                $localPath === 'messages/zh-TW/event.php' ||
                $localPath === 'messages/zh-TW/fest.php' ||
                $localPath === 'messages/zh-TW/festpower2.php' ||
                $localPath === 'messages/zh-TW/freshness2.php' ||
                $localPath === 'messages/zh-TW/gear.php' ||
                $localPath === 'messages/zh-TW/gear2.php' ||
                $localPath === 'messages/zh-TW/gearstat.php' ||
                $localPath === 'messages/zh-TW/link.php' ||
                $localPath === 'messages/zh-TW/map.php' ||
                $localPath === 'messages/zh-TW/map2.php' ||
                $localPath === 'messages/zh-TW/privacy.php' ||
                $localPath === 'messages/zh-TW/rank.php' ||
                $localPath === 'messages/zh-TW/rank2.php' ||
                $localPath === 'messages/zh-TW/region.php' ||
                $localPath === 'messages/zh-TW/rule.php' ||
                $localPath === 'messages/zh-TW/rule2.php' ||
                $localPath === 'messages/zh-TW/salmon-boss2.php' ||
                $localPath === 'messages/zh-TW/salmon-event2.php' ||
                $localPath === 'messages/zh-TW/salmon-history2.php' ||
                $localPath === 'messages/zh-TW/salmon-map2.php' ||
                $localPath === 'messages/zh-TW/salmon-tide2.php' ||
                $localPath === 'messages/zh-TW/salmon-title2.php' ||
                $localPath === 'messages/zh-TW/salmon2.php' ||
                $localPath === 'messages/zh-TW/slack.php' ||
                $localPath === 'messages/zh-TW/special.php' ||
                $localPath === 'messages/zh-TW/special2.php' ||
                $localPath === 'messages/zh-TW/start.php' ||
                $localPath === 'messages/zh-TW/subweapon.php' ||
                $localPath === 'messages/zh-TW/subweapon2.php' ||
                $localPath === 'messages/zh-TW/ua_vars.php' ||
                $localPath === 'messages/zh-TW/ua_vars_v.php' ||
                $localPath === 'messages/zh-TW/version2.php' ||
                $localPath === 'messages/zh-TW/weapon.php' ||
                $localPath === 'messages/zh-TW/weapon2.php'
            ) {
                return true;
            }
        }
        return false;
        // }}}
    }

    public function actionMachineTranslation(): int
    {
        $result = 0;
        $deepl = Yii::createObject(['class' => i18n\DeeplTranslator::class]);
        $result |= $deepl->run() ? 0 : 1;

        $opencc = Yii::createObject(['class' => i18n\OpenCCTranslator::class]);
        return $result | $opencc->run() ? 0 : 1;
    }
}
