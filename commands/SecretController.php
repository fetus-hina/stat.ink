<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;

class SecretController extends Controller
{
    public function actionCookie()
    {
        $this->stdout("Creating secret key file \"config/cookie-secret.php\"... ", Console::FG_YELLOW);
        $length = 32;
        $binLength = (int)ceil($length * 3 / 4);
        $binary = random_bytes($binLength); // PHP 7 native random_bytes() or compat-lib's one
        $key = substr(strtr(base64_encode($binary), '+/=', '_-.'), 0, $length);
        file_put_contents(
            __DIR__ . '/../config/cookie-secret.php',
            sprintf("<?php\nreturn '%s';\n", $key)
        );
        $this->stdout("Done.\n", Console::FG_GREEN);
    }
}
