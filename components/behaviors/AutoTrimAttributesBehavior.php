<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\behaviors;

use yii\base\Model;

class AutoTrimAttributesBehavior extends TrimAttributesBehavior
{
    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => [$this, 'trim'],
        ];
    }

    public function trim() : void
    {
        $targets = array_keys($this->owner->attributes);
        foreach ($targets as $attrName) {
            $this->owner->{$attrName} = $this->doTrim($this->owner->{$attrName});
        }
    }
}
