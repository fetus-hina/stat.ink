<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use app\models\WeaponCategory2;
use yii\console\Controller;
use yii\helpers\Console;

class Api2MarkdownController extends Controller
{
    public function actionWeapon() : int
    {
        $compats = [
            'maneuver_collabo' => 'manueuver_collabo',
            'maneuver' => 'manueuver',
        ];
        $categories = WeaponCategory2::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        foreach ($categories as $category) {
            $types = $category->getWeaponTypes()
                ->orderBy(['id' => SORT_ASC])
                ->all();
            foreach ($types as $type) {
                $weapons = $type->getWeapons()
                    ->orderBy(['key' => SORT_ASC])
                    ->asArray()
                    ->all();
                foreach ($weapons as $weapon) {
                    $remarks = [];
                    if (isset($compats[$weapon['key']])) {
                        $remarks[] = sprintf(
                            '互換性のため %s も受け付けます',
                            implode(', ', array_map(
                                function (string $value) : string {
                                    return '`' . $value . '`';
                                },
                                (array)$compats[$weapon['key']]
                            ))
                        );
                        $remarks[] = sprintf(
                            'Also accepts %s for compatibility',
                            implode(', ', array_map(
                                function (string $value) : string {
                                    return '`' . $value . '`';
                                },
                                (array)$compats[$weapon['key']]
                            ))
                        );
                    }
                    printf(
                        "|`%s`|`%s`|%s<br>%s|%s|\n",
                        $weapon['key'],
                        isset($weapon['splatnet']) ? (string)(int)$weapon['splatnet'] : '',
                        Yii::t('app-weapon2', $weapon['name'], [], 'ja-JP'),
                        $weapon['name'],
                        implode('<br>', $remarks)
                    );
                }
            }
        }
        return 0;
    }
}
