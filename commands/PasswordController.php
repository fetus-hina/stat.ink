<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Random\Randomizer;
use app\components\helpers\Password;
use yii\console\Controller;

use function fprintf;
use function implode;
use function preg_match;
use function printf;

use const STDERR;

class PasswordController extends Controller
{
    public const PASSWORD_LENGTH = 32;

    public $defaultAction = 'generate';

    public function actionGenerate(int $num = 1): int
    {
        for ($i = 0; $i < $num; ++$i) {
            if ($i > 0) {
                echo "\n";
            }
            printf("New password (%d/%d):\n", $i + 1, $num);

            if (!$password = $this->generate()) {
                fprintf(STDERR, "Failed to generate.\n");
                return 1;
            }

            printf("    password = %s\n", $password);
            printf("    hash = %s\n", Password::hash($password));
        }

        return 0;
    }

    protected function generate(): string
    {
        $passwordChars = implode('', [
            '!%&()*+,-./:;<=>?_',
            '0123456789',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'abcdefghijklmnopqrstuvwxyz',
        ]);
        $randomizer = new Randomizer();

        while (true) {
            $password = $randomizer->getBytesFromString($passwordChars, static::PASSWORD_LENGTH);

            if (
                preg_match('/[0-9]/', $password) &&
                preg_match('/[A-Z]/', $password) &&
                preg_match('/[a-z]/', $password) &&
                preg_match('/[^0-9A-Za-z]/', $password)
            ) {
                return $password;
            }
        }
    }
}
