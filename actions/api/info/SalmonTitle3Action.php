<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\Language;
use app\models\SalmonTitle3;
use yii\base\Action;
use yii\helpers\ArrayHelper;

use function strnatcasecmp;

use const SORT_ASC;

final class SalmonTitle3Action extends Action
{
    public function run()
    {
        return $this->controller->render('salmon-title3', [
            'titles' => SalmonTitle3::find()
                ->with(['salmonTitle3Aliases'])
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
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
