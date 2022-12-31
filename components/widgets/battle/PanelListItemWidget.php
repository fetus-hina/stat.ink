<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\battle;

use Yii;
use app\components\widgets\battle\panelItem\BattleItem1Widget;
use app\components\widgets\battle\panelItem\BattleItem2Widget;
use app\components\widgets\battle\panelItem\BattleItem3Widget;
use app\components\widgets\battle\panelItem\SalmonItem2Widget;
use app\components\widgets\battle\panelItem\SalmonItem3Widget;
use app\models\Battle;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Salmon2;
use app\models\Salmon3;
use yii\base\Widget;

final class PanelListItemWidget extends Widget
{
    /**
     * @var array<class-string, class-string>
     */
    public array $itemClasses = [];

    /**
     * @var Battle|Battle2|Battle3|Salmon2|Salmon3
     */
    public $model;

    public function init()
    {
        parent::init();

        if (!$this->itemClasses) {
            $this->itemClasses = [
                Battle::class  => BattleItem1Widget::class,
                Battle2::class => BattleItem2Widget::class,
                Battle3::class => BattleItem3Widget::class,
                Salmon2::class => SalmonItem2Widget::class,
                Salmon3::class => SalmonItem3Widget::class,
            ];
        }
    }

    public function run()
    {
        $implClass = $this->itemClasses[\get_class($this->model)];
        return \call_user_func(
            [$implClass, 'widget'],
            ['model' => $this->model],
        );
    }
}
