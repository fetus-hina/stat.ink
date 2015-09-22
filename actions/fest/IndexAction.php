<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Fest;

class IndexAction extends BaseAction
{
    public function run()
    {
        return $this->controller->render('index.tpl', [
            'allFest' => Fest::find()->orderBy('fest.id DESC')->all(),
        ]);
    }
}
