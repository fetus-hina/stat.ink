<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\models\Salmon2;
use yii\console\Controller;
use yii\helpers\Console;

class Salmon2Controller extends Controller
{
    public function actionDelete(int $id): int
    {
        $transaction = Yii::$app->db->beginTransaction();
        $salmon = Salmon2::findOne(['id' => $id]);
        if (!$salmon) {
            $this->stderr("Could not find specified results \"{$id}\"\n", Console::FG_RED);
            return 1;
        }
        $salmon->delete();
        $transaction->commit();
        $this->stderr("updated.\n");
        return 0;
    }
}
