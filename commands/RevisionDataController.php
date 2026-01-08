<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DateTime;
use Yii;
use app\components\Version;
use yii\console\Controller;
use yii\helpers\Json;

use function addslashes;
use function array_shift;
use function file_put_contents;
use function fwrite;
use function implode;
use function is_array;
use function is_bool;
use function is_int;
use function ltrim;
use function sprintf;
use function str_repeat;
use function vsprintf;

use const STDERR;

class RevisionDataController extends Controller
{
    public function actionIndex(): int
    {
        $data = [
            'Version' => Version::getVersion(),
            'Revision' => vsprintf('%s (%s)', [
                Version::getShortRevision() ?: 'UNKNOWN',
                Version::getRevision() ?: 'UNKNOWN',
            ]),
            'Last Committed' => Version::getLastCommited()->format(DateTime::ATOM) ?? 'UNKNOWN',
            'Version Tags' => Json::encode(Version::getVersionTags()),
        ];

        foreach ($data as $key => $value) {
            fwrite(STDERR, $key . ': ' . $value . "\n");
        }

        return 0;
    }

    public function actionUpdate(): int
    {
        $success = true;
        $success &= $this->updateVersionConf();
        $success &= $this->updateRevisionConf();
        return $success ? 0 : 1;
    }

    private function updateVersionConf(): bool
    {
        $version = '';
        if ($tags = Version::getVersionTags()) {
            $version = ltrim(array_shift($tags), 'v');
        }

        if ($version === '') {
            $version = 'DEVELOPMENT';
        }

        file_put_contents(
            Yii::getAlias('@app/config/version.php'),
            implode("\n", [
                '<?php',
                '',
                'declare(strict_types=1);',
                '',
                sprintf('return \'%s\';', addslashes($version)),
            ]) . "\n",
        );

        return true;
    }

    private function updateRevisionConf(): bool
    {
        $commit = Version::getLastCommited();

        $data = [
            'lastCommitted' => $commit ? $commit->format(DateTime::ATOM) : null,
            'lastCommittedT' => $commit ? (int)$commit->format('U') : null,
            'longHash' => Version::getRevision(),
            'shortHash' => Version::getShortRevision(),
            'tags' => Version::getVersionTags(),
        ];

        $contents = implode("\n", [
            '<?php',
            '',
            'declare(strict_types=1);',
            '',
            'return ' . static::format($data) . ';',
            '',
        ]);

        file_put_contents(
            Yii::getAlias('@app/config/git-revision.php'),
            $contents,
        );

        return true;
    }

    private static function format($data, int $indentLevel = 0): string
    {
        $indent = str_repeat(' ', $indentLevel * 4);
        $indent1 = $indent . '    ';

        if (is_array($data)) {
            $result = [];
            $result[] = '[';
            foreach ($data as $key => $value) {
                if (is_int($key)) {
                    $result[] = $indent1 . static::format($value, $indentLevel + 1) . ',';
                } else {
                    $result[] = $indent1 . static::format($key) . ' => ' .
                        static::format($value, $indentLevel + 1) . ',';
                }
            }
            $result[] = $indent . ']';
            return implode("\n", $result);
        } elseif ($data === null) {
            return 'null';
        } elseif (is_int($data)) {
            return (string)$data;
        } elseif (is_bool($data)) {
            return $data ? 'true' : 'false';
        } else {
            return "'" . addslashes((string)$data) . "'";
        }
    }
}
