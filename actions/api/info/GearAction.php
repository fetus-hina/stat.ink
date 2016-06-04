<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\info;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\components\helpers\Translator;
use app\models\Language;
use app\models\Gear;
use app\models\GearType;

class GearAction extends BaseAction
{
    public $type;

    public function getType()
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
        $gears = array_map(
            function (array $gear) : array {
                return [
                    'key'   => $gear['key'],
                    'name'  => Yii::t('app-gear', $gear['name']),
                    'names' => Translator::translateToAll('app-gear', $gear['name']),
                    'brand' => Yii::t('app-brand', $gear['brand']['name'] ?? null),
                    'ability' => Yii::t('app-ability', $gear['ability']['name'] ?? null),
                ];
            },
            Gear::find()
                ->with(['brand', 'ability'])
                ->andWhere(['type_id' => $type->id])
                ->asArray()
                ->all()
        );
        usort($gears, function (array $a, array $b) : int {
            return strnatcasecmp($a['name'], $b['name']);
        });

        $langs = Language::find()->asArray()->all();
        $sysLang = Yii::$app->language;
        usort($langs, function (array $a, array $b) use ($sysLang) : int {
            if ($a['lang'] === $sysLang) {
                return -1;
            }
            if ($b['lang'] === $sysLang) {
                return 1;
            }
            return strnatcasecmp($a['name'], $b['name']);
        });

        return $this->controller->render('gear.tpl', [
            'type'  => $type,
            'types' => GearType::find()->asArray()->orderBy('[[id]] ASC')->all(),
            'langs' => $langs,
            'gears' => $gears,
        ]);
    }
}
