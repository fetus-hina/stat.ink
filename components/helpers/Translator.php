<?php
namespace app\components\helpers;

use Yii;
use app\models\Language;

class Translator
{
    private static $langs = null;

    public function translateToAll($category, $message, $params = [])
    {
        if (self::$langs === null) {
            self::$langs = Language::find()->all();
        }
        $i18n = Yii::$app->i18n;
        $ret = [];
        foreach (self::$langs as $lang) {
            $ret[strtr($lang->lang, '-', '_')] = $i18n->translate($category, $message, $params, $lang->lang);
        }
        return $ret;
    }
}
