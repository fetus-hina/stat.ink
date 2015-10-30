<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Zend\Crypt\FileCipher;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\components\helpers\Resource;

class BackupController extends Controller
{
    public $defaultAction = 'save';

    public function actionSave()
    {
        $outPath = sprintf(
            '%s/statink-%s.dump.xz.aes',
            Yii::getAlias('@app/runtime/backup'),
            date('YmdHis', time())
        );
        if (!file_exists(dirname($outPath))) {
            mkdir(dirname($outPath), 0755, true);
        }
        $tmpPath = new Resource(
            tempnam('backup-', sys_get_temp_dir()),
            'unlink'
        );

        $this->stdout("Dumping database... ", Console::FG_YELLOW);
        $execinfo = $this->createDumpCommandLine($tmpPath->get());
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
            $execinfo['env']
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

        $this->stdout("Encrypting dump file... ", Console::FG_YELLOW);
        $crypt = new FileCipher();
        $crypt->setKey(
            include(
                Yii::getAlias('@app/config/backup-secret.php')
            )
        );
        if (!$crypt->encrypt($tmpPath->get(), $outPath)) {
            $this->stdout("ERROR\n", Console::FG_RED);
            return 1;
        }
        $this->stdout("SUCCESS\n", Console::FG_GREEN);
        return 0;
    }

    private function createDumpCommandLine($outPath)
    {
        $config = include(__DIR__ . '/../config/db.php');
        $dsn = $this->parseDsn($config['dsn']);
        $cmdline = sprintf(
            '/usr/bin/env %s -F c -Z 0 %s %s -U %s %s | xz -6 > %s',
            escapeshellarg('pg_dump'),
            @$dsn['host'] ? '-h ' . escapeshellarg($dsn['host']) : '',
            @$dsn['port'] ? '-p ' . escapeshellarg($dsn['port']) : '',
            escapeshellarg($config['username']),
            escapeshellarg($dsn['dbname']),
            escapeshellarg($outPath)
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
