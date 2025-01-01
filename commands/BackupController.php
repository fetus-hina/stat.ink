<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DirectoryIterator;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use function array_reverse;
use function array_slice;
use function count;
use function date;
use function dirname;
use function escapeshellarg;
use function fclose;
use function file_exists;
use function getcwd;
use function mkdir;
use function parse_str;
use function preg_match;
use function proc_close;
use function proc_open;
use function rsort;
use function sprintf;
use function str_replace;
use function stream_get_contents;
use function time;
use function unlink;

class BackupController extends Controller
{
    public const KEEP_ENTRIES = 7;

    public $defaultAction = 'save';

    public function actionSave(bool $cleanup = true)
    {
        $outPath = sprintf(
            '%s/statink-%s.dump.xz.gpg',
            Yii::getAlias('@app/runtime/backup'),
            date('YmdHis', time()),
        );
        if (!file_exists(dirname($outPath))) {
            mkdir(dirname($outPath), 0755, true);
        }

        $this->stdout('Dumping database... ', Console::FG_YELLOW);
        $execinfo = $this->createDumpCommandLine($outPath);
        $this->stdout("\n" . $execinfo['cmdline'] . "\n");
        $descriptorspec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
        ];
        $pipes = [];
        $proc = @proc_open(
            $execinfo['cmdline'],
            $descriptorspec,
            $pipes,
            getcwd(),
            $execinfo['env'],
        );
        if (!$proc) {
            $this->stdout("ERROR\n", Console::FG_RED);
            return 1;
        }
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $status = proc_close($proc);
        if ($status !== 0) {
            $this->stdout("ERROR\n", Console::FG_RED);
            return 1;
        }
        $this->stdout("SUCCESS\n", Console::FG_GREEN);

        if ($cleanup) {
            return $this->actionCleanup();
        }

        return 0;
    }

    public function actionCleanup()
    {
        $dirPath = Yii::getAlias('@app/runtime/backup');
        if (!file_exists($dirPath)) {
            return 0;
        }

        $files = [];
        $it = new DirectoryIterator($dirPath);
        foreach ($it as $entry) {
            if (
                $entry->isFile() &&
                preg_match(
                    '/^statink-\d+\.dump\.xz\.(?:aes|gpg)$/',
                    $entry->getBasename(),
                    $match,
                )
            ) {
                $files[] = $entry->getPathname();
            }
        }

        rsort($files);
        if (count($files) > static::KEEP_ENTRIES) {
            $targets = array_reverse(array_slice($files, static::KEEP_ENTRIES));
            foreach ($targets as $path) {
                $this->stderr("DELETE {$path}\n");
                @unlink($path);
            }
        }

        return 0;
    }

    private function createDumpCommandLine($outPath)
    {
        $config = include __DIR__ . '/../config/db.php';
        $gpg = include __DIR__ . '/../config/backup-gpg.php';
        $dsn = $this->parseDsn($config['dsn']);
        $cmdline = sprintf(
            '/usr/bin/env %s -F c -Z 0 %s %s -U %s %s | %s -6 | %s -e -r %s --compress-algo %s --cipher-algo %s -o %s',
            escapeshellarg('pg_dump'),
            @$dsn['host'] ? '-h ' . escapeshellarg($dsn['host']) : '',
            @$dsn['port'] ? '-p ' . escapeshellarg($dsn['port']) : '',
            escapeshellarg($config['username']),
            escapeshellarg($dsn['dbname']),
            escapeshellarg('xz'),
            escapeshellarg('gpg2'),
            escapeshellarg($gpg['userId']), // recipient (gpg)
            escapeshellarg('none'), // compress algo (gpg)
            escapeshellarg('AES256'), // cipher algo (gpg)
            escapeshellarg($outPath),
        );
        $env = [
            'PGPASSWORD' => $config['password'],
        ];
        $copies = [
            'CPATH',
            'JAVACONFDIRS',
            'LD_LIBRARY_PATH',
            'LIBRARY_PATH',
            'MANPATH',
            'PATH',
            'PKG_CONFIG_PATH',
            'XDG_CONFIG_DIRS',
            'XDG_DATA_DIRS',
        ];
        foreach ($copies as $key) {
            if (isset($_SERVER[$key])) {
                $env[$key] = $_SERVER[$key];
            }
        }
        return [
            'cmdline' => $cmdline,
            'env' => $env,
        ];
    }

    private function parseDsn($dsn)
    {
        $dsn = str_replace('pgsql:', '', $dsn);
        $dsn = str_replace(';', '&', $dsn);
        $opts = [];
        parse_str($dsn, $opts);
        return $opts;
    }
}
