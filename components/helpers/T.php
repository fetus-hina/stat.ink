<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use TypeError;
use app\components\web\Application as WebApplication;
use app\components\web\Controller as WebControllerEx;
use yii\base\Application as BaseApplication;
use yii\base\Controller as BaseController;
use yii\web\Controller as WebController;

final class T
{
    public static function webApplication(BaseApplication $obj): WebApplication
    {
        return self::is(WebApplication::class, $obj);
    }

    public static function webController(BaseController $obj): WebController
    {
        return self::is(WebController::class, $obj);
    }

    public static function webControllerEx(BaseController $obj): WebControllerEx
    {
        return self::is(WebControllerEx::class, $obj);
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     * @return U
     */
    public static function is(string $class, $obj): object
    {
        if (!$obj instanceof $class) {
            throw new TypeError();
        }

        return $obj;
    }
}
