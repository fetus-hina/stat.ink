<?php
/**
 * @copyright Copyright (C) 2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets\battle;

use Yii;
use app\models\Battle2;
use app\models\Battle;
use yii\base\Widget;

class BattleItemWidget extends Widget
{
    public $itemClasses;
    public $model;

    public function init()
    {
        parent::init();
        if (!$this->itemClasses) {
            $this->itemClasses = [
                Battle::class  => item\BattleItem1Widget::class,
                Battle2::class => item\BattleItem2Widget::class,
            ];
        }
    }

    public function run()
    {
        $implClass = $this->itemClasses[get_class($this->model)];
        return call_user_func([$implClass, 'widget'], ['model' => $this->model]);
    }
}
