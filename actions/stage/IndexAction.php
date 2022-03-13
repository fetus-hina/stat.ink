<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\stage;

use app\components\helpers\T;
use yii\base\Action;
use yii\web\Response;

final class IndexAction extends Action
{
    public function run(): Response
    {
        // イカリング1が2017-09に死んだのでそれを最終としてそこに飛ばす
        return T::webController($this->controller)
            ->redirect(['stage/month',
                'year' => 2017,
                'month' => 9,
            ]);
    }
}
