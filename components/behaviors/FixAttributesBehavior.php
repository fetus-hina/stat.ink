<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;

class FixAttributesBehavior extends Behavior
{
    // $attributes = [
    //   'attrName' => [
    //     'wrong' => 'fixed',
    //     'wrong2' => 'fixed2',
    //   ],
    // ]
    public $attributes;
    public $trigger = Model::EVENT_BEFORE_VALIDATE;

    public function events()
    {
        return [
            $this->trigger => [$this, 'doFix'],
        ];
    }

    public function doFix(): void
    {
        foreach ($this->attributes as $attrName => $fixData) {
            $value = $this->owner->{$attrName};
            if (isset($fixData[trim($value)])) {
                $this->owner->{$attrName} = $fixData[trim($value)];
                Yii::info(
                    vsprintf('%s fixed %s to %s', [
                        $attrName,
                        $value,
                        $fixData[trim($value)],
                    ]),
                    __METHOD__,
                );
            }
        }
    }
}
