<?php

/**
 * @copyright Copyright (C) 2025-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use DirectoryIterator;
use RuntimeException;
use Yii;
use app\components\helpers\TypeHelper;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function array_map;
use function array_merge;
use function array_values;
use function basename;
use function chdir;
use function escapeshellarg;
use function exec;
use function fclose;
use function feof;
use function fgets;
use function fopen;
use function getcwd;
use function implode;
use function in_array;
use function natsort;
use function str_starts_with;
use function trim;
use function vsprintf;

final class AuditController extends Controller
{
    private const TARGET_DIRECTORY = '@app/data/audit';

    public $defaultAction = 'audit';

    public function actionAudit(): int
    {
        $ok = true;

        foreach ($this->getAuditFiles() as $path) {
            if (!$this->doAudit($path)) {
                $ok = false;
            }
        }

        return $ok ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * @return string[]
     */
    private function getAuditFiles(): array
    {
        $dir = TypeHelper::string(Yii::getAlias(self::TARGET_DIRECTORY));

        $files = [];
        foreach (new DirectoryIterator($dir) as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }

            $files[] = $entry->getPathname();
        }

        natsort($files);

        return array_values($files);
    }

    private function doAudit(string $path): bool
    {
        $this->stderr('Starting audit for file: ' . basename($path) . "\n");

        $ok = true;

        $ourPackages = $this->getInstalledPackages();

        $fh = fopen($path, 'r');
        if (!$fh) {
            $this->stderr('Failed to open audit file: ' . basename($path) . "\n");
            return false;
        }
        try {
            while (!feof($fh)) {
                $line = trim((string)fgets($fh));
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                if (in_array($line, $ourPackages, true)) {
                    $this->stderr('ERROR: Found malicious package: ' . $line . "\n");
                    $ok = false;
                }
            }
        } finally {
            @fclose($fh);
        }

        return $ok;
    }

    /**
     * @return string[]
     */
    private function getInstalledPackages(): array
    {
        static $cache = false;
        if ($cache === false) {
            $list = array_merge(
                $this->getComposerInstalledPackages(),
                $this->getNpmInstalledPackages(),
            );

            natsort($list);
            $cache = array_values($list);
        }

        return $cache;
    }

    /**
     * @return string[]
     */
    private function getComposerInstalledPackages(): array
    {
        $jsonText = $this->execCommand(['./composer.phar', 'show', '--locked', '--format', 'json']);
        $json = Json::decode($jsonText);
        return ArrayHelper::getColumn(
            TypeHelper::array(ArrayHelper::getValue($json, 'locked')),
            function (mixed $item): string {
                $item = TypeHelper::array($item);
                return vsprintf('composer::%s@%s', [
                    TypeHelper::string($item['name']),
                    TypeHelper::string($item['version']),
                ]);
            },
        );
    }

    /**
     * @return string[]
     */
    private function getNpmInstalledPackages(): array
    {
        $jsonText = $this->execCommand(['npm', 'ls', '--json', '--all']);
        $json = Json::decode($jsonText);
        $result = [];

        $dependencies = TypeHelper::array(ArrayHelper::getValue($json, 'dependencies'));
        foreach ($dependencies as $name => $info) {
            $result[] = vsprintf('npm::%s@%s', [
                $name,
                TypeHelper::string(
                    ArrayHelper::getValue(
                        TypeHelper::array($info),
                        'version',
                    ),
                ),
            ]);
        }

        natsort($result);
        return array_values($result);
    }

    /**
     * @param string[] $command
     */
    private function execCommand(array $command): string
    {
        $pwd = TypeHelper::string(getcwd());
        try {
            chdir(TypeHelper::string(Yii::getAlias('@app')));

            $output = [];
            $status = ExitCode::OK;
            TypeHelper::string(
                exec(
                    implode(
                        ' ',
                        array_map(
                            escapeshellarg(...),
                            $command,
                        ),
                    ),
                    $output,
                    $status,
                ),
            );

            if ($status !== ExitCode::OK) {
                throw new RuntimeException(
                    vsprintf('Command failed with status %d: %s', [
                        $status,
                        implode(' ', $command),
                    ]),
                );
            }

            return implode("\n", $output) . "\n";
        } finally {
            @chdir($pwd);
        }
    }
}
