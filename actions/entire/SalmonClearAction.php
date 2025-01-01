<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use app\models\StatSalmon2ClearRate;
use yii\web\ViewAction;

use const SORT_ASC;

class SalmonClearAction extends ViewAction
{
    public function run()
    {
        return $this->controller->render('salmon-clear', [
            'models' => StatSalmon2ClearRate::find()
                ->with('stage')
                ->orderBy(['stage_id' => SORT_ASC])
                ->all(),
        ]);
    }
}
