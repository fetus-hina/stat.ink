<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Language;

class I18n
{
    public static function languageLinkTags()
    {
        $controller = Yii::$app->controller;
        $request = Yii::$app->request;
        if (!$controller || !$request || !$request->isGet) {
            return '';
        }
        $params = $request->get();
        unset($params['_lang_']);

        if (!$route = $controller->route) {
            return '';
        }


        $ret = [];
        foreach (Language::find()->asArray()->all() as $lang) {
            $newParams = array_merge(
                [$route, '_lang_' => $lang['lang']],
                $params
            );
            $ret[] = Html::tag(
                'link',
                '',
                [
                    'rel' => 'alternate',
                    'hreflang' => $lang['lang'],
                    'href' => Url::to($newParams, true),
                ]
            );
        }
        return implode("\n", $ret) . "\n";
    }
}
