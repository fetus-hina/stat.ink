<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use app\components\db\Connection;
use yii\console\Controller;
use yii\helpers\Console;

class SecretController extends Controller
{
    public function actionCookie()
    {
        $this->stdout('Creating secret key file "config/cookie-secret.php"... ', Console::FG_YELLOW);
        $length = 32;
        $binLength = (int)ceil($length * 3 / 4);
        $binary = random_bytes($binLength);
        $key = substr(strtr(base64_encode($binary), '+/=', '_-.'), 0, $length);
        file_put_contents(
            __DIR__ . '/../config/cookie-secret.php',
            sprintf("<?php\nreturn '%s';\n", $key)
        );
        $this->stdout("Done.\n", Console::FG_GREEN);
    }

    public function actionAuthkey()
    {
        $this->stdout('Creating secret key file "config/authkey-secret.php"... ', Console::FG_YELLOW);
        $length = 64;
        $binLength = (int)ceil($length * 3 / 4);
        $binary = random_bytes($binLength);
        $key = substr(strtr(base64_encode($binary), '+/=', '_-.'), 0, $length);
        file_put_contents(
            __DIR__ . '/../config/authkey-secret.php',
            sprintf("<?php\nreturn '%s';\n", $key)
        );
        $this->stdout("Done.\n", Console::FG_GREEN);
    }

    public function actionDb(string $host = 'localhost', ?string $password = null)
    {
        $this->stdout('Creating "config/db.php"... ', Console::FG_YELLOW);
        if ($password == '') {
            $passwordBits = 128;
            $length = (int)ceil($passwordBits / 8);
            $binary = random_bytes($length); // PHP 7 native random_bytes() or compat-lib's one
            $password = substr(strtr(base64_encode($binary), '+/=', '_-.'), 0, $length);
        }

        $dsnOptions = [
            'host' => $host,
            'port' => '5432',
            'dbname' => 'statink',
        ];

        $options = [
            'class' => Connection::class,
            'dsn' => $this->makeDsn('pgsql', $dsnOptions),
            'username' => 'statink',
            'password' => $password,
            'charset' => 'UTF-8',
            'enableSchemaCache' => true,
            'schemaCache' => 'schemaCache',
        ];

        $file  = "<?php\n";
        $file .= "return [\n";
        foreach ($options as $k => $v) {
            if (is_bool($v)) {
                $file .= "    '{$k}' => " . ($v ? 'true' : 'false') . ",\n";
            } else {
                $file .= "    '{$k}' => '" . addslashes($v) . "',\n";
            }
        }
        $file .= "];\n";
        file_put_contents(
            __DIR__ . '/../config/db.php',
            $file
        );
        $this->stdout("Done.\n", Console::FG_GREEN);
    }

    private function makeDsn($driver, array $options)
    {
        return $driver . ':' . http_build_query($options, '', ';');
    }
}
