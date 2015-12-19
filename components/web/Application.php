<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\web\Application as Base;

class Application extends Base
{
    private $region = 'jp';

    public function setSplatoonRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    public function getSplatoonRegion()
    {
        return $this->region;
    }
}
