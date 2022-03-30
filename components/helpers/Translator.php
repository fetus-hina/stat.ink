<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\models\Language;

use const SORT_ASC;

class Translator
{
    private static $langs = null;

    public static function translateToAll(
        string $category,
        string $message,
        array $params = []
    ): array {
        if (self::$langs === null) {
            // @phpstan-ignore-next-line
            self::$langs = Language::find()
                ->standard()
                ->orderBy(['lang' => SORT_ASC])
                ->all();
        }

        $i18n = Yii::$app->i18n;
        $ret = [];
        foreach (self::$langs as $lang) {
            $key = strtr($lang->getLanguageId(), '-', '_');
            $ret[$key] = $i18n->translate($category, $message, $params, $lang->lang);
        }
        return $ret;
    }
}
