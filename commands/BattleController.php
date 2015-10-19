<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;
use app\models\Battle;

class BattleController extends Controller
{
    public function actionDelete($id)
    {
        $battle = Battle::findOne(['id' => (int)(string)$id]);
        if (!$battle) {
            $this->stderr("Could not find specified battle \"{$id}\"\n", Console::FG_RED);
            return 1;
        }

        $battle->delete();
    }
}
