<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers\db;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use yii\db\Expression;

use function microtime;

class Now extends Expression
{
    public function __construct()
    {
        $time = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        $strtime = DateTimeFormatter::unixTimeToString(
            $time,
            new DateTimeZone('Etc/UTC'),
        );
        parent::__construct(Yii::$app->db->quoteValue($strtime));
    }
}
