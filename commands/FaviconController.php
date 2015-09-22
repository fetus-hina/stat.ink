<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use Zend\Crypt\FileCipher;

class FaviconController extends Controller
{
    public function actionEncrypt()
    {
        if (!$licenseKey = $this->readLicenseKey()) {
            $this->stdout("Favicon artwork license key is not exist (or broken).\n", Console::FG_RED);
            return 2;
        }
        $engine = $this->createCryptEngine();
        $engine->setKey($licenseKey);
        $status = $engine->encrypt(
            Yii::getAlias('@app/data/favicon/ikagirl.png'),
            Yii::getAlias('@app/data/favicon/ikagirl.dat')
        );
        if (!$status) {
            $this->stdout("Failed to create ikagirl.dat\n", Console::FG_RED);
            @unlink(Yii::getAlias('@app/data/ikagirl.dat'));
            return 1;
        }
        $this->stdout("Created ikagirl.dat\n", Console::FG_GREEN);
    }

    public function actionDecrypt()
    {
        if (!$licenseKey = $this->readLicenseKey()) {
            $this->stdout("SKIPPED (Favicon artwork license key is not exist or broken.)\n", Console::FG_YELLOW);
            return;
        }
        @unlink(Yii::getAlias('@app/data/favicon/ikagirl.png'));
        $engine = $this->createCryptEngine();
        $engine->setKey($licenseKey);
        $status = $engine->decrypt(
            Yii::getAlias('@app/data/favicon/ikagirl.dat'),
            Yii::getAlias('@app/data/favicon/ikagirl.png')
        );
        if (!$status) {
            $this->stdout("Failed to create ikagirl.png\n", Console::FG_RED);
            @unlink(Yii::getAlias('@app/data/favicon/ikagirl.png'));
            return 1;
        }
        $this->stdout("Created ikagirl.png\n", Console::FG_GREEN);
    }

    private function createCryptEngine()
    {
        $engine = new FileCipher();
        $engine->setKeyIteration(65535);
        $engine->setHashAlgorithm('sha256');
        $engine->setPbkdf2HashAlgorithm('sha256');
        return $engine;
    }

    private function readLicenseKey()
    {
        $path = Yii::getAlias('@app/config/favicon.license.txt');
        if (!file_exists($path) || !is_readable($path)) {
            return false;
        }
        $key = trim(file_get_contents($path, false, null));
        if (!preg_match('/^[!-~]{32}$/', $key)) {
            return false;
        }
        return $key;
    }
}
