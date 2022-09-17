<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\battle;

use Yii;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Battle;
use app\models\Salmon2;
use yii\base\Widget;
use yii\bootstrap\Html;

class PanelListItemWidget extends Widget
{
    public $itemClasses;
    public $model;

    public function init()
    {
        parent::init();
        if (!$this->itemClasses) {
            $this->itemClasses = [
                Battle::class  => panelItem\BattleItem1Widget::class,
                Battle2::class => panelItem\BattleItem2Widget::class,
                Battle3::class => panelItem\BattleItem3Widget::class,
                Salmon2::class => panelItem\SalmonItem2Widget::class,
            ];
        }
    }

    public function run()
    {
        $implClass = $this->itemClasses[get_class($this->model)];
        return call_user_func([$implClass, 'widget'], ['model' => $this->model]);
    }
}
