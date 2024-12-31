<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DateTimeImmutable;
use DateTimeZone;
use DirectoryIterator;
use Iterator;
use Yii;
use app\components\helpers\GitAuthorHelper;
use app\models\Language;
use yii\console\Controller;

use function array_diff;
use function array_keys;
use function array_reduce;
use function count;
use function dirname;
use function escapeshellarg;
use function exec;
use function file_exists;
use function file_put_contents;
use function implode;
use function min;
use function mkdir;
use function passthru;
use function preg_match;
use function setlocale;
use function str_replace;
use function strcmp;
use function strnatcasecmp;
use function strtolower;
use function time;
use function uksort;
use function vsprintf;

use const LC_COLLATE;

final class I18nController extends Controller
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
        Yii::$app->timeZone = 'Asia/Tokyo';

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
            'ko-KR' => 'ko',
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

        // $changed |= str_contains($outPath, '/fr/');

        if (!$changed && count($current) > 0) {
            $this->stderr("  => SKIP\n");
            return true;
        }

        setlocale(LC_COLLATE, 'C');
        uksort($current, fn (string $a, string $b): int => strnatcasecmp($a, $b) ?: strcmp($a, $b));

        $esc = fn (string $text): string => str_replace(['\\', "'"], ['\\\\', "\\'"], $text);

        $now = new DateTimeImmutable('now', new DateTimeZone(Yii::$app->timeZone));
        $commitAt = $now->setTimestamp($this->getGitFirstCommitTime($outPath));

        $file = [];
        $file[] = '<?php';
        $file[] = '';
        $file[] = '/**';
        $file[] = vsprintf(' * @copyright Copyright (C) %s AIZAWA Hina', [
            $now->format('Y') === $commitAt->format('Y')
                ? $now->format('Y')
                : vsprintf('%s-%s', [$commitAt->format('Y'), $now->format('Y')]),
        ]);
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
            $file[] = vsprintf("    '%s' => '%s',", [
                $esc($key),
                $esc($value),
            ]);
        }
        $file[] = '];';
        file_put_contents($outPath, implode("\n", $file) . "\n");
        $this->stderr("  => SAVED!\n");
        return true;
    }

    private function kickSupportActions(bool $strongUpdate): int
    {
        $cmdline = vsprintf('/usr/bin/env %s/yii splatoon2-ink-i18n/index %d', [
            Yii::getAlias('@app'),
            $strongUpdate ? 1 : 0,
        ]);
        passthru($cmdline, $status1);

        $cmdline = vsprintf('/usr/bin/env %s/yii api2-markdown', [
            Yii::getAlias('@app'),
        ]);
        passthru($cmdline, $status2);

        return $status1 | $status2;
    }

    private function getGitFirstCommitTime(string $path): int
    {
        $cmdline = vsprintf('/usr/bin/env git log --pretty=%s -- %s', [
            escapeshellarg('%at%n%ct'),
            escapeshellarg($path),
        ]);
        $status = null;
        $lines = [];
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr("Could not get commits\n");
            exit(1);
        }

        return array_reduce(
            $lines,
            fn (int $carry, string $line): int => min($carry, (int)$line),
            time(),
        );
    }

    /**
     * @return string[]
     */
    private function getGitContributors(string $path): array
    {
        return array_keys(
            GitAuthorHelper::getAuthors($path),
        );
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
