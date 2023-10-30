<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\components\helpers\Translator;
use app\models\Language;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function strnatcasecmp;
use function usort;

use const SORT_ASC;

class Weapon2Action extends BaseAction
{
    public function run()
    {
        $categories = array_map(
            fn (WeaponCategory2 $category): array => [
                'key' => $category->key,
                'name' => Yii::t('app-weapon2', $category->name),
                'types' => array_map(
                    fn (WeaponType2 $type): array => [
                        'key' => $type->key,
                        'name' => Yii::t('app-weapon2', $type->name),
                        'weapons' => array_map(
                            fn (Weapon2 $weapon): array => [
                                'canonical' => Yii::t('app-weapon2', $weapon->canonical->name),
                                'canonicalKey' => $weapon->canonical->key,
                                'key' => $weapon->key,
                                'mainPowerUp' => Yii::t('app-ability2', $weapon->mainPowerUp->name),
                                'mainReference' => Yii::t('app-weapon2', $weapon->mainReference->name),
                                'mainReferenceKey' => $weapon->mainReference->key,
                                'name' => Yii::t('app-weapon2', $weapon->name),
                                'names' => Translator::translateToAll('app-weapon2', $weapon->name),
                                'special' => Yii::t('app-special2', $weapon->special->name),
                                'specialKey' => $weapon->special->key,
                                'splatnet' => $weapon->splatnet,
                                'sub' => Yii::t('app-subweapon2', $weapon->subweapon->name),
                                'subKey' => $weapon->subweapon->key,
                            ],
                            $type->weapons,
                        ),
                    ],
                    $category->weaponTypes,
                ),
            ],
            WeaponCategory2::find()
                ->with([
                    'weaponTypes' => function (Query $query): void {
                        $query->orderBy([
                            'category_id' => SORT_ASC,
                            'rank' => SORT_ASC,
                        ]);
                    },
                    'weaponTypes.weapons' => function (Query $query): void {
                        $query->orderBy([
                            'key' => SORT_ASC,
                        ]);
                    },
                    'weaponTypes.weapons.canonical',
                    'weaponTypes.weapons.mainPowerUp',
                    'weaponTypes.weapons.mainReference',
                    'weaponTypes.weapons.special',
                    'weaponTypes.weapons.subweapon',
                ])
                ->orderBy([
                    'id' => SORT_ASC,
                ])
                ->all(),
        );

        $langs = Language::find()->standard()->asArray()->all();
        $sysLang = Yii::$app->language;
        usort($langs, function (array $a, array $b) use ($sysLang): int {
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
