<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use app\assets\JdenticonAsset;
use yii\base\Widget;
use yii\helpers\Html;

class JdenticonWidget extends Widget
{
    public $hash;
    public $size;
    public $class;
    public $params = [];
    public $vector = true;

    public function run()
    {
        JdenticonAsset::register($this->view);

        if (!is_string($this->hash) || !preg_match('/^[0-9a-f]{32,}$/', $this->hash)) {
            throw new \Exception('JdenticonWidget::$hash must be set');
        }

        $params = (array)$this->params;
        if ($this->size > 0) {
            $params['width'] = (string)(int)$this->size;
            $params['height'] = $params['width'];
        }
        if ($this->class !== null) {
            $params['class'] = $params['class'];
        }
        if (!isset($params['data'])) {
            $params['data'] = [];
        }
        $params['data']['jdenticon-hash'] = $this->hash;

        return Html::tag(
            $this->vector ? 'svg' : 'canvas',
            '',
            $params
        );
    }
}
