<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\info;

use Yii;
use app\components\helpers\Translator;
use app\models\Language;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use yii\web\ViewAction as BaseAction;

class Weapon2Action extends BaseAction
{
    public function run()
    {
        $categories = array_map(
            function (WeaponCategory2 $category) : array {
                return [
                    'key' => $category->key,
                    'name' => Yii::t('app-weapon2', $category->name),
                    'types' => array_map(
                        function (WeaponType2 $type) : array {
                            return [
                                'key' => $type->key,
                                'name' => Yii::t('app-weapon2', $type->name),
                                'weapons' => array_map(
                                    function (Weapon2 $weapon) : array {
                                        return [
                                            'key' => $weapon->key,
                                            'splatnet' => $weapon->splatnet,
                                            'name' => Yii::t('app-weapon2', $weapon->name),
                                            'names' => Translator::translateToAll('app-weapon2', $weapon->name),
                                            'sub' => Yii::t('app-subweapon2', $weapon->subweapon->name),
                                            'special' => Yii::t('app-special2', $weapon->special->name),
                                            'mainReference' => Yii::t('app-weapon2', $weapon->mainReference->name),
                                            'canonical' => Yii::t('app-weapon2', $weapon->canonical->name),
                                        ];
                                    },
                                    $type->weapons
                                ),
                            ];
                        },
                        $category->weaponTypes
                    ),
                ];
            },
            WeaponCategory2::find()
                ->with([
                    'weaponTypes' => function ($query) {
                        $query->orderBy('[[id]] ASC');
                    },
                    'weaponTypes.weapons' => function ($query) {
                        $query->orderBy('[[key]] ASC');
                    },
                    'weaponTypes.weapons.subweapon',
                    'weaponTypes.weapons.special',
                    'weaponTypes.weapons.mainReference',
                    'weaponTypes.weapons.canonical',
                ])
                ->orderBy('[[id]] ASC')
                ->all()
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

        return $this->controller->render('weapon2', [
            'categories' => $categories,
            'langs' => $langs,
        ]);
    }
}
