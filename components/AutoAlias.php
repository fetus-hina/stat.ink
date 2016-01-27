<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components;

use Yii;
use yii\base\Component;

class AutoAlias extends Component
{
    public $aliases;

    public function init()
    {
        parent::init();
        if (is_array($this->aliases)) {
            foreach ($this->aliases as $k => $v) {
                Yii::setAlias(
                    $k,
                    Yii::getAlias(is_callable($v) ? $v() : $v)
                );
            }
        }
    }
}
