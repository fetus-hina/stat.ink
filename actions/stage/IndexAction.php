<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\stage;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class IndexAction extends BaseAction
{
    // 将来的にはここにちゃんといい感じの一覧ページを作る
    // とりあえず今月のステージランキングに飛ばす
    public function run()
    {
        $time = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $this->controller->redirect([
            'stage/month',
            'year' => (int)date('Y', $time),
            'month' => (int)date('n', $time),
        ]);
    }
}
