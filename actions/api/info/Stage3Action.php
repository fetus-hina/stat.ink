<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\BigrunMap3;
use app\models\Language;
use app\models\Map3;
use app\models\SalmonMap3;
use yii\web\ViewAction;

use function array_values;
use function strcmp;
use function strnatcasecmp;
use function usort;

final class Stage3Action extends ViewAction
{
    public function run()
    {
        $langs = Language::find()->standard()->all();
        $sysLang = Yii::$app->language;

        usort(
            $langs,
            function (Language $a, Language $b) use ($sysLang): int {
                if ($a->lang === $sysLang) {
                    return -1;
                }

                if ($b->lang === $sysLang) {
                    return 1;
                }

                return strnatcasecmp($a->name, $b->name);
            },
        );

        return $this->controller->render('stage3', [
            'bigrunStages' => $this->sortedMaps(BigrunMap3::find()->with('bigrunMap3Aliases')->all()),
            'langs' => $langs,
            'salmonStages' => $this->sortedMaps(SalmonMap3::find()->with('salmonMap3Aliases')->all()),
            'stages' => $this->sortedMaps(Map3::find()->with('map3Aliases')->all()),
        ]);
    }

    /**
     * @param (BigrunMap3|Map3|SalmonMap3)[] $list
     * @return (BigrunMap3|Map3|SalmonMap3)[]
     */
    private function sortedMaps(array $list): array
    {
        usort(
            $list,
            fn (BigrunMap3|Map3|SalmonMap3 $a, BigrunMap3|Map3|SalmonMap3 $b): int => strnatcasecmp($a->name, $b->name)
                    ?: strnatcasecmp($a->key, $b->key)
                    ?: strcmp($a->key, $b->key),
        );

        return array_values($list);
    }
}
