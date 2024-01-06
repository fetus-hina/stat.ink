<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\asset;

use ParagonIE\ConstantTime\Base32;
use Random\Engine\Secure;
use Random\Randomizer;
use Yii;
use app\components\helpers\TypeHelper;
use yii\base\Action;
use yii\console\ExitCode;

use function escapeshellarg;
use function exec;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function fprintf;
use function hex2bin;
use function implode;
use function is_readable;
use function is_string;
use function preg_match;
use function sprintf;
use function trim;

use const STDERR;

final class UpRevisionAction extends Action
{
    /**
     * Update revision number of assets
     *
     * You should be update the number on deploy action.
     * The number will be used in public asset path.
     *
     * @return int
     */
    public function run()
    {
        $path = (string)Yii::getAlias('@app/config/asset-revision.php');
        $revision = $this->getRevision();

        $php = [];
        $php[] = '<?php';
        $php[] = '';
        $php[] = 'declare(strict_types=1);';
        $php[] = '';
        $php[] = '// This config file is updated by `yii asset/up-revision`.';
        $php[] = '// DO NOT EDIT';
        $php[] = '';
        $php[] = sprintf("return '%s';", $revision);

        file_put_contents($path, implode("\n", $php) . "\n");
        fprintf(STDERR, "Asset revision is updated to %s.\n", $revision);

        return ExitCode::OK;
    }

    private function getRevision(): string
    {
        return $this->getRevisionByDeployerFile()
            ?? $this->getRevisionByGit()
            ?? $this->generateRandomRevision();
    }

    private function getRevisionByDeployerFile(): ?string
    {
        $path = (string)Yii::getAlias('@app/REVISION');
        if (file_exists($path) && is_readable($path)) {
            $revision = trim((string)file_get_contents($path));
            if (preg_match('/^[0-9a-f]{40,}$/i', $revision)) {
                return $this->makeGitShortRevision($revision);
            }
        }

        return null;
    }

    private function getRevisionByGit(): ?string
    {
        $cmdline = implode(' ', [
            '/usr/bin/env',
            escapeshellarg('git'),
            escapeshellarg('rev-parse'),
            escapeshellarg('HEAD'),
            '2>/dev/null',
        ]);
        $line = @exec($cmdline, $lines, $status);
        if ($status === ExitCode::OK && is_string($line)) {
            $revision = trim((string)$line);
            if (preg_match('/^[0-9a-f]{40,}$/i', $revision)) {
                return $this->makeGitShortRevision($revision);
            }
        }

        return null;
    }

    private function generateRandomRevision(): string
    {
        $randomizer = new Randomizer(new Secure());
        return $this->makeGitShortRevision($randomizer->getRandomBytes(20));
    }

    private function makeGitShortRevision(string $revision): string
    {
        return Base32::encodeUnpadded(
            TypeHelper::string(hex2bin($revision)),
        );
    }
}
