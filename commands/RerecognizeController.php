<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Yii;
use app\models\BattleRerecognizeForm;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\helpers\Json;

use function feof;
use function fgets;
use function fopen;
use function fwrite;
use function trim;

use const STDERR;

class RerecognizeController extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate($filename)
    {
        if (!$fh = @fopen($filename, 'rt')) {
            fwrite(STDERR, "Could not open $filename\n");
            return 1;
        }

        $lineNo = 0;
        while (!feof($fh)) {
            $line = trim(fgets($fh));
            ++$lineNo;
            if ($line === '') {
                continue;
            }
            try {
                $data = Json::decode($line);
            } catch (InvalidParamException $e) {
                fwrite(STDERR, "line {$lineNo}: " . $e->getMessage());
                continue;
            }

            $transaction = Yii::$app->db->beginTransaction();
            $form = new BattleRerecognizeForm();
            $form->attributes = $data;
            if (!$form->validate() || !$form->save()) {
                fwrite(STDERR, "line {$lineNo}:\n");
                foreach ($form->getFirstErrors() as $k => $v) {
                    fwrite(STDERR, "  {$k}: {$v}\n");
                }
                $transaction->rollback();
                continue;
            } else {
                $transaction->commit();
            }
        }
    }
}
