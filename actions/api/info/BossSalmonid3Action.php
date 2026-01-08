<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\Language;
use app\models\SalmonBoss3;
use app\models\SalmonKing3;
use yii\base\Action;
use yii\helpers\ArrayHelper;

use function strcmp;
use function strnatcasecmp;

final class BossSalmonid3Action extends Action
{
    public function run()
    {
        return $this->controller->render('boss-salmonid3', [
            'bosses' => $this->getBossSalmonids(),
            'kings' => $this->getKingSalmonids(),
            'langs' => $this->getLangs(),
        ]);
    }

    /**
     * @return Language[]
     */
    private function getLangs(): array
    {
        $sysLang = Yii::$app->language;
        return ArrayHelper::sort(
            Language::find()->standard()->all(),
            fn (Language $a, Language $b): int => ($a->lang === $sysLang ? -1 : 0)
                ?: ($b->lang === $sysLang ? 1 : 0)
                ?: strnatcasecmp($a->name, $b->name),
        );
    }

    /**
     * @return SalmonBoss3[]
     */
    private function getBossSalmonids(): array
    {
        return ArrayHelper::sort(
            SalmonBoss3::find()->with(['salmonBoss3Aliases'])->all(),
            function (SalmonBoss3 $a, SalmonBoss3 $b): int {
                $na = Yii::t('app-salmon-boss3', $a->name);
                $nb = Yii::t('app-salmon-boss3', $b->name);

                return strnatcasecmp($na, $nb)
                    ?: strcmp($na, $nb)
                    ?: strnatcasecmp($a->name, $b->name)
                    ?: strcmp($a->name, $b->name);
            },
        );
    }

    /**
     * @return SalmonKing3[]
     */
    private function getKingSalmonids(): array
    {
        return ArrayHelper::sort(
            SalmonKing3::find()->with(['salmonKing3Aliases'])->all(),
            function (SalmonKing3 $a, SalmonKing3 $b): int {
                $na = Yii::t('app-salmon-boss3', $a->name);
                $nb = Yii::t('app-salmon-boss3', $b->name);

                return strnatcasecmp($na, $nb)
                    ?: strcmp($na, $nb)
                    ?: strnatcasecmp($a->name, $b->name)
                    ?: strcmp($a->name, $b->name);
            },
        );
    }
}
