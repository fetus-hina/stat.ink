<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\Salmon2ClearStats;
use yii\web\ViewAction;

class SalmonClearAction extends ViewAction
{
    public function run()
    {
        $models = Salmon2ClearStats::all();
        return $this->controller->render('salmon-clear', [
            'models' => $models,
        ]);
    }
}
