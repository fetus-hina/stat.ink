<?php
/**
 * @copyright Copyright (C) 2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets\battle;

use Yii;
use app\assets\BattleThumbListAsset;
use yii\base\Widget;
use yii\bootstrap\Html;

class BattleListWidget extends Widget
{
    public $template;
    public $itemClass;
    public $models = []; // array (or iteratable) of Battle | Battle2

    public function init()
    {
        parent::init();
        if ($this->template == '') {
            $this->template = Html::tag('ul', '{items}', ['class' => 'battles']);
        }
        if (!$this->itemClass) {
            $this->itemClass = BattleItemWidget::class;
        }
    }

    public function run()
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            BattleThumbListAsset::register($this->view);

            $replace = [
                '{items}' => $this->renderItems(),
            ];
            return preg_replace_callback(
                '/\{\w+\}/',
                function (array $match) use ($replace): string {
                    return $replace[$match[0]] ?? $match[0];
                },
                $this->template
            );
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    protected function renderItems(): string
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $ret = [];
            foreach ($this->models as $model) {
                $ret[] = Html::tag(
                    'li',
                    call_user_func([$this->itemClass, 'widget'], ['model' => $model])
                );
            }
            return implode('', $ret);
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }
}
