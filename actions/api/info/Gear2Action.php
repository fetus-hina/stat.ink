<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\info;

use Yii;
use app\components\helpers\Translator;
use app\models\Gear2;
use app\models\GearType;
use app\models\Language;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class Gear2Action extends BaseAction
{
    public $type;

    public function getType() : GearType
    {
        return GearType::findOne(['key' => (string)$this->type]);
    }

    public function init()
    {
        parent::init();
        if (!$this->getType()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run()
    {
        $type = $this->getType();
        $gears = $type->getGear2s()->with(['brand', 'ability'])->all();
        usort($gears, function (Gear2 $a, Gear2 $b) : int {
            return strnatcasecmp(
                $a->translatedName,
                $b->translatedName
            );
        });
        $langs = Language::find()->standard()->all();
        $sysLang = Yii::$app->language;
        usort($langs, function (Language $a, Language $b) use ($sysLang) : int {
            if ($a->lang === $b->lang) {
                return 0;
            } elseif ($a->lang === $sysLang) {
                return -1;
            } elseif ($b->lang === $sysLang) {
                return 1;
            } else {
                return strnatcasecmp($a->name, $b->name);
            }
        });
        return $this->controller->render('gear2', [
            'type'  => $type,
            'langs' => $langs,
            'gears' => $gears,
        ]);
    }
}
