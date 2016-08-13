<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use yii\web\HttpException;

class ServiceUnavailableHttpException extends HttpException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(503, $message, $code, $previous);
    }
}
