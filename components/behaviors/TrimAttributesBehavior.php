<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\base\Model;

class TrimAttributesBehavior extends Behavior
{
    public $targets = [];

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => [$this, 'trim'],
        ];
    }

    public function trim() : void
    {
        foreach ($this->targets as $attrName) {
            $this->owner->{$attrName} = $this->doTrim($this->owner->{$attrName});
        }
    }

    protected function doTrim($value)
    {
        if (is_scalar($value)) {
            $value = trim((string)$value);
            return $value === '' ? null : $value;
        } elseif (is_array($value) || $value instanceof \Traversable) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doTrim($v);
            }
            return $value;
        } else {
            return $value;
        }
    }
}
