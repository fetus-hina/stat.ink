<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\Ability3;
use app\models\Language;
use yii\web\ViewAction;

use const SORT_ASC;

final class Ability3Action extends ViewAction
{
    public function run()
    {
        $langs = Language::find()->standard()->all();
        $abilities = Ability3::find()
            ->orderBy(['rank' => SORT_ASC])
            ->all();
        $sysLang = Yii::$app->language;

        \usort($langs, function (Language $a, Language $b) use ($sysLang): int {
            if ($a->lang === $sysLang) {
                return -1;
            }

            if ($b->lang === $sysLang) {
                return 1;
            }

            return strnatcasecmp($a->name, $b->name);
        });

        return $this->controller->render('ability3', [
            'abilities' => $abilities,
            'langs' => $langs,
        ]);
    }
}
