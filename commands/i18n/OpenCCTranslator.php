<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\i18n;

use DirectoryIterator;
use Exception;
use Normalizer;
use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;

class OpenCCTranslator extends Component
{
    private const INPUT_DIR = '@app/messages/_deepl/zh';
    private const OUTPUT_DIR = '@app/messages/_deepl/zh-TW';
    private const OPENCC_CONFIG = 's2t.json';

    public function run(): bool
    {
        setlocale(LC_ALL, 'C');

        $status = true;
        foreach ($this->getTargetFiles() as $inputPath) {
            if (
                !$this->translateFile(
                    $inputPath,
                    Yii::getAlias(static::OUTPUT_DIR) . '/' . basename($inputPath),
                )
            ) {
                $status = false;
            }
        }
        return $status;
    }

    private function getTargetFiles()
    {
        $it = new DirectoryIterator(Yii::getAlias(static::INPUT_DIR));
        foreach ($it as $entry) {
            if (
                $entry->isDot() ||
                $entry->isDir() ||
                strtolower((string)$entry->getExtension()) !== 'php'
            ) {
                continue;
            }

            yield $entry->getPathname();
        }
    }

    private function translateFile(string $inputPath, string $outputPath): bool
    {
        fwrite(STDERR, 'Processing zh-CN to zh-TW with OpenCC: ' . basename($inputPath) . "\n");

        if (!FileHelper::createDirectory(dirname($outputPath))) {
            fwrite(STDERR, 'Could not create directory: ' . dirname($outputPath) . "\n");
            return false;
        }

        $result = true;
        $inputTexts = include $inputPath;
        $outputTexts = [];
        foreach ($inputTexts as $enText => $hansText) {
            fwrite(STDERR, "  {$hansText}\n");
            $hantText = $this->translate($hansText);
            $outputTexts[$enText] = $hantText;
        }

        $esc = fn (string $text): string => str_replace(['\\', "'"], ['\\\\', "\\'"], $text);

        fwrite(STDERR, "Writing...\n");
        $fh = fopen($outputPath, 'wt');
        fwrite($fh, "<?php\n\n");
        fwrite($fh, "/**\n");
        vfprintf($fh, " * @copyright Copyright (C) 2015-%d AIZAWA Hina\n", [
            gmdate('Y', time() + 9 * 3600), // JST
        ]);
        vfprintf($fh, " * @license %s MIT\n", [
            'https://github.com/fetus-hina/stat.ink/blob/master/LICENSE',
        ]);
        fwrite($fh, " * @author AIZAWA Hina <hina@fetus.jp>\n");
        fwrite($fh, " */\n\n");
        fwrite($fh, "declare(strict_types=1);\n\n");
        fwrite($fh, "return [\n");
        foreach ($outputTexts as $en => $localized) {
            vfprintf($fh, "    '%s' => '%s',\n", [
                $esc($en),
                $esc($localized),
            ]);
        }
        fwrite($fh, "];\n");
        fclose($fh);
        fwrite(STDERR, "  -- Wrote!\n");

        return true;
    }

    private function translate(string $hansText): string
    {
        $cmdline = vsprintf('/usr/bin/env %s -c %s', [
            escapeshellarg('opencc'),
            escapeshellarg(static::OPENCC_CONFIG),
        ]);

        $descSpec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
        ];
        $handle = proc_open($cmdline, $descSpec, $pipes);
        if (!is_resource($handle)) {
            throw new Exception('Could not create a process. Is opencc installed?');
        }

        fwrite($pipes[0], $hansText . "\n");
        fclose($pipes[0]);

        $hantText = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $status = proc_close($handle);
        if ($status !== 0) {
            throw new Exception('opencc exit with status ' . $status);
        }

        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                Normalizer::normalize($hantText, Normalizer::FORM_C),
            ),
        );
    }
}
