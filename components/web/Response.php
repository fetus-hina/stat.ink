<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\web\Response as Base;

class Response extends Base
{
    private $isBuffering = false;

    public function init()
    {
        parent::init();
        $this->on(Base::EVENT_BEFORE_SEND, function ($event) {
            $this->isBuffering = false;
            if (headers_sent()) {
                return;
            }
            if (ob_start('ob_gzhandler')) {
                $this->isBuffering = true;
            }
        });
        $this->on(Base::EVENT_AFTER_SEND, function ($event) {
            if ($this->isBuffering) {
                ob_end_flush();
                $this->isBuffering = false;
            }
        });
    }
}
