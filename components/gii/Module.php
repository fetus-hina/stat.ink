<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\gii;

use Yii;
use yii\gii\Module as BaseModule;

class Module extends BaseModule
{
    protected function coreGenerators()
    {
        return array_merge(parent::coreGenerators(), [
            'model' => generators\model\Generator::class,
        ]);
    }
}
