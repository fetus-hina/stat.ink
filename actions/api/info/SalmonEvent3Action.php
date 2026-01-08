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
use app\models\SalmonEvent3;
use yii\base\Action;
use yii\helpers\ArrayHelper;

use function strcmp;
use function strnatcasecmp;

final class SalmonEvent3Action extends Action
{
    public function run()
    {
        return $this->controller->render('salmon-event3', [
            'events' => ArrayHelper::sort(
                SalmonEvent3::find()
                    ->with(['salmonEvent3Aliases'])
                    ->all(),
                function (SalmonEvent3 $a, SalmonEvent3 $b): int {
                    $aN = Yii::t('app-salmon-event3', $a->name);
                    $bN = Yii::t('app-salmon-event3', $b->name);
                    return strnatcasecmp($aN, $bN)
                        ?: strcmp($aN, $bN)
                        ?: strnatcasecmp($a->name, $b->name)
                        ?: strcmp($a->name, $b->name);
                },
            ),
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
}
