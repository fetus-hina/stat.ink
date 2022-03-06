<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use Traversable;
use yii\base\Behavior;
use yii\base\Model;

class TrimAttributesBehavior extends Behavior
{
    public $targets;
    public $recursive;

    public function init()
    {
        parent::init();
        if (!is_array($this->targets)) {
            $this->targets = [];
        }
        if ($this->recursive !== false) {
            $this->recursive = true;
        }
    }

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => [$this, 'trim'],
        ];
    }

    public function trim(): void
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
        } elseif (is_array($value) || $value instanceof Traversable) {
            if ($this->recursive) {
                foreach ($value as $k => $v) {
                    $value[$k] = $this->doTrim($v);
                }
            }
            return $value;
        } else {
            return $value;
        }
    }
}
