<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DirectoryIterator;
use S3;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

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

    public function actionUpload()
    {
        $this->stdout('Uploading dump files... ', Console::FG_YELLOW);
        $config = include Yii::getAlias('@app/config/backup-s3.php');
        if (!$config['endpoint'] || !$config['accessKey'] || !$config['secret'] || !$config['bucket']) {
            $this->stdout("NOT CONFIGURED.\n", Console::FG_PURPLE);
            return 0;
        }
        $files = [];
        $it = new DirectoryIterator(Yii::getAlias('@app/runtime/backup'));
        foreach ($it as $entry) {
            if ($entry->isDot() || !$entry->isFile()) {
                continue;
            }
            if (!preg_match('/^statink-\d+\.dump\.xz\.(?:aes|gpg)$/', $entry->getBasename())) {
                continue;
            }
            $files[] = $entry->getPathname();
        }
        sort($files);
        if (empty($files)) {
            $this->stdout("NO FILE EXISTS\n", Console::FG_PURPLE);
            return 0;
        }
        $this->stdout(count($files) . " file(s).\n", Console::FG_GREEN);
        S3::setEndpoint($config['endpoint']);
        S3::setAuth($config['accessKey'], $config['secret']);
        S3::setSSL(true, strpos($config['endpoint'], 'amazonaws') !== false);
        S3::setExceptions(true);
        foreach ($files as $i => $path) {
            if (preg_match('/^statink-(\d{4}\d{2})(\d{2})\d+\.dump\.xz\.(?:aes|gpg)$/', basename($path), $match)) {
                $objName = sprintf('%1$s/%1$s%2$s/%3$s', $match[1], $match[2], basename($path));
            } else {
                $objName = basename($path);
            }
            $this->stdout(sprintf('  [%d / %d] ', $i + 1, count($files)));
            $this->stdout(basename($path) . ' ', Console::FG_GREEN);
            $this->stdout('(' . number_format(filesize($path)) . " bytes)\n      as ");
            $this->stdout($objName, Console::FG_GREEN);
            $this->stdout("...\n");

            $file = S3::inputFile($path, true);
            $t1 = microtime(true);
            $ret = S3::putObject($file, $config['bucket'], $objName, S3::ACL_PRIVATE, [], [], S3::STORAGE_CLASS_RRS);
            $t2 = microtime(true);
            if (!$ret) {
                $this->stdout("    ERROR\n", Console::FG_RED);
                return 1;
            }
            $this->stdout('    SUCCESS ', Console::FG_GREEN);
            $this->stdout(sprintf("in %.3fsec\n", $t2 - $t1));

            unlink($path);
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
