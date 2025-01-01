<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm\dropdownList;

use Yii;
use app\models\Battle3FilterForm;
use app\models\Special3;
use app\models\Subweapon3;
use app\models\User;
use app\models\UserWeapon3;
use app\models\Weapon3;
use app\models\WeaponType3;
use yii\helpers\ArrayHelper;

use function array_merge;
use function sprintf;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;
use const SORT_LOCALE_STRING;

trait WeaponDropdownListTrait
{
    public function getWeaponDropdown(?User $user): array
    {
        return [
            array_merge(
                $this->getFrequentlyUsedWeaponDropdown($user),
                $this->getWeaponTypeDropdown(),
                $this->getSubweaponDropdown(),
                $this->getSpecialDropdown(),
            ),
            ['prompt' => Yii::t('app-weapon3', 'Any Weapon')],
        ];
    }

    private function getFrequentlyUsedWeaponDropdown(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $list = UserWeapon3::find()
            ->with(['weapon'])
            ->andWhere(['{{%user_weapon3}}.[[user_id]]' => $user->id])
            ->orderBy([
                'battles' => SORT_DESC,
                'last_used_at' => SORT_DESC,
                'weapon_id' => SORT_DESC,
            ])
            ->limit(10)
            ->all();
        if (!$list) {
            return [];
        }

        $fmt = Yii::$app->formatter;
        return [
            Yii::t('app', 'Favorite Weapons') => ArrayHelper::map(
                $list,
                'weapon.key',
                fn (UserWeapon3 $model): string => vsprintf('%s (%s)', [
                    Yii::t('app-weapon3', $model->weapon->name),
                    $fmt->asInteger((int)$model->battles),
                ]),
            ),
        ];
    }

    private function getWeaponTypeDropdown(): array
    {
        $weapons = ArrayHelper::map(
            Weapon3::find()
                ->with(['mainweapon'])
                ->all(),
            'key',
            fn (Weapon3 $model): string => Yii::t('app-weapon3', $model->name),
            fn (Weapon3 $model): int => $model->mainweapon->type_id,
        );

        return ArrayHelper::map(
            WeaponType3::find()->orderBy(['rank' => SORT_ASC])->all(),
            fn (WeaponType3 $model): string => Yii::t('app-weapon3', $model->name),
            fn (WeaponType3 $model): array => $this->renderWeaponTypeList($model, $weapons),
        );
    }

    /**
     * @param array<int, array<string, string>> $weapons
     */
    private function renderWeaponTypeList(WeaponType3 $type, array $weapons): array
    {
        /**
         * @var string $typeKey e.g., "@shooter"
         */
        $typeKey = sprintf('%s%s', Battle3FilterForm::PREFIX_WEAPON_TYPE, $type->key);

        return array_merge(
            [
                $typeKey => Yii::t('app-weapon3', 'All of {0}', [
                    Yii::t('app-weapon3', $type->name),
                ]),
            ],
            ArrayHelper::asort(
                ArrayHelper::getValue($weapons, $type->id),
                SORT_LOCALE_STRING,
            ),
        );
    }

    private function getSubweaponDropdown(): array
    {
        return [
            Yii::t('app', 'Sub Weapon') => ArrayHelper::asort(
                ArrayHelper::map(
                    Subweapon3::find()->all(),
                    fn (Subweapon3 $model): string => vsprintf('%s%s', [
                        Battle3FilterForm::PREFIX_WEAPON_SUB,
                        $model->key,
                    ]),
                    fn (Subweapon3 $model): string => Yii::t('app-subweapon3', $model->name),
                ),
                SORT_LOCALE_STRING,
            ),
        ];
    }

    private function getSpecialDropdown(): array
    {
        return [
            Yii::t('app', 'Special') => ArrayHelper::asort(
                ArrayHelper::map(
                    Special3::find()->all(),
                    fn (Special3 $model): string => vsprintf('%s%s', [
                        Battle3FilterForm::PREFIX_WEAPON_SPECIAL,
                        $model->key,
                    ]),
                    fn (Special3 $model): string => Yii::t('app-special3', $model->name),
                ),
                SORT_LOCALE_STRING,
            ),
        ];
    }
}
