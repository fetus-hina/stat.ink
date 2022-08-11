<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\components\helpers\Translator;
use app\models\Language;
use app\models\Special3;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class Weapon3Action extends Action
{
    public function run()
    {
        return $this->controller->render('weapon3', [
            'langs' => $this->getLangs(),
            'specials' => $this->getSpecials(),
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
            fn (Language $a, Language $b): int => (($a->lang === $sysLang) ? -1 : 0)
                ?: (($b->lang === $sysLang) ? 1 : 0)
                ?: \strnatcasecmp($a->name, $b->name),
        );
    }

    private function getSpecials(): array
    {
        return ArrayHelper::sort(
            Special3::find()->with(['special3Aliases'])->all(),
            function (Special3 $a, Special3 $b): int {
                $aN = Yii::t('app-special3', $a->name);
                $bN = Yii::t('app-special3', $b->name);
                return \strnatcasecmp($aN, $bN)
                    ?: \strcmp($aN, $bN)
                    ?: \strnatcasecmp($a->name, $b->name)
                    ?: \strcmp($a->name, $b->name);
            },
        );
    }
}
