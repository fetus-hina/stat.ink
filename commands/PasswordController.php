<?php

/**
 * @copyright Copyright (C) 2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use app\components\helpers\Password;
use yii\console\Controller;

use function ceil;
use function fprintf;
use function implode;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function printf;
use function random_bytes;
use function strlen;
use function substr;

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
        $passwordCharsCount = strlen($passwordChars);
        $filterRegex = '/[^' . preg_quote($passwordChars, '/') . ']+/';

        retry:

        $password = '';
        $generateLength = static::PASSWORD_LENGTH;
        do {
            $randomLength = (int)ceil($generateLength * 256 / $passwordCharsCount * 1.1);
            // printf("残り %d 文字、 %d バイト取得\n", $generateLength, $randomLength);
            $password .= preg_replace($filterRegex, '', random_bytes($randomLength));
            $generateLength = static::PASSWORD_LENGTH - strlen($password);
        } while ($generateLength > 0);

        $password = substr($password, 0, static::PASSWORD_LENGTH);

        if (
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[^0-9A-Za-z]/', $password)
        ) {
            goto retry;
        }

        return $password;
    }
}
