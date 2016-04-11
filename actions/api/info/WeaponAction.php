<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\info;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\components\helpers\Translator;
use app\models\Language;
use app\models\Weapon;
use app\models\WeaponType;

class WeaponAction extends BaseAction
{
    public function run()
    {
        $types = array_map(
            function (array $type) : array {
                return [
                    'key'   => $type['key'],
                    'name'  => Yii::t('app-weapon', $type['name']),
                    'weapons' => array_map(
                        function (array $weapon) : array {
                            return [
                                'key' => $weapon['key'],
                                'names' => Translator::translateToAll('app-weapon', $weapon['name']),
                            ];
                        },
                        Weapon::find()
                            ->andWhere(['type_id' => $type['id']])
                            ->orderBy('[[key]] ASC')
                            ->asArray()
                            ->all()
                    ),
                ];
            },
            WeaponType::find()->orderBy('[[id]] ASC')->asArray()->all()
        );

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

        return $this->controller->render('weapon.tpl', [
            'types' => $types,
            'langs' => $langs,
        ]);
    }
}
