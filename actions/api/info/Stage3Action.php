<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\Language;
use app\models\Map3;
use yii\web\ViewAction;

final class Stage3Action extends ViewAction
{
    public function run()
    {
        $langs = Language::find()->standard()->all();
        $stages = Map3::find()
            ->with('map3Aliases')
            ->all();
        $sysLang = Yii::$app->language;

        usort($langs, function (Language $a, Language $b) use ($sysLang): int {
            if ($a->lang === $sysLang) {
                return -1;
            }
            if ($b->lang === $sysLang) {
                return 1;
            }
            return strnatcasecmp($a->name, $b->name);
        });

        usort($stages, fn (Map3 $a, Map3 $b): int => strnatcasecmp(Yii::t('app-map3', $a->name), Yii::t('app-map3', $b->name))
                ?: strnatcasecmp($a->name, $b->name)
                ?: strnatcasecmp($a->key, $b->key)
                ?: strcmp($a->key, $b->key));

        return $this->controller->render('stage3', [
            'stages' => $stages,
            'langs' => $langs,
        ]);
    }
}
