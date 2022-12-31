<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\info;

use Yii;
use app\models\Language;
use app\models\SalmonWeapon3;
use app\models\Special3;
use app\models\Subweapon3;
use app\models\Weapon3;
use yii\base\Action;
use yii\helpers\ArrayHelper;

final class Weapon3Action extends Action
{
    public function run()
    {
        return $this->controller->render('weapon3', [
            'langs' => $this->getLangs(),
            'salmons' => $this->getSalmonWeapons(),
            'specials' => $this->getSpecials(),
            'subs' => $this->getSubweapons(),
            'weapons' => $this->getWeapons(),
        ]);
    }

    /**
     * @return Language[]
     */
    private function getLangs(): array
    {
        $sysLang = Yii::$app->language;
        return ArrayHelper::sort(
            Language::find()->standard()->all(),
            fn (Language $a, Language $b): int => ($a->lang === $sysLang ? -1 : 0)
                ?: ($b->lang === $sysLang ? 1 : 0)
                ?: \strnatcasecmp($a->name, $b->name),
        );
    }

    private function getSpecials(): array
    {
        return ArrayHelper::sort(
            Special3::find()->with(['special3Aliases'])->all(),
            function (Special3 $a, Special3 $b): int {
                $aN = Yii::t('app-special3', $a->name);
                $bN = Yii::t('app-special3', $b->name);
                return \strnatcasecmp($aN, $bN)
                    ?: \strcmp($aN, $bN)
                    ?: \strnatcasecmp($a->name, $b->name)
                    ?: \strcmp($a->name, $b->name);
            },
        );
    }

    private function getSubweapons(): array
    {
        return ArrayHelper::sort(
            Subweapon3::find()->with(['subweapon3Aliases'])->all(),
            function (Subweapon3 $a, Subweapon3 $b): int {
                $aN = Yii::t('app-subweapon3', $a->name);
                $bN = Yii::t('app-subweapon3', $b->name);
                return \strnatcasecmp($aN, $bN)
                    ?: \strcmp($aN, $bN)
                    ?: \strnatcasecmp($a->name, $b->name)
                    ?: \strcmp($a->name, $b->name);
            },
        );
    }

    private function getWeapons(): array
    {
        return ArrayHelper::sort(
            Weapon3::find()
                ->with([
                    'mainweapon',
                    'mainweapon.type',
                    'salmonWeapon3',
                    'special',
                    'subweapon',
                    'weapon3Aliases',
                ])
                ->all(),
            function (Weapon3 $a, Weapon3 $b): int {
                $aN = Yii::t('app-weapon3', $a->name);
                $bN = Yii::t('app-weapon3', $b->name);
                return $a->mainweapon->type->rank <=> $b->mainweapon->type->rank
                    ?:  \strnatcasecmp($aN, $bN)
                    ?: \strcmp($aN, $bN)
                    ?: \strnatcasecmp($a->name, $b->name)
                    ?: \strcmp($a->name, $b->name);
            },
        );
    }

    private function getSalmonWeapons(): array
    {
        return ArrayHelper::sort(
            SalmonWeapon3::find()
                ->with(['salmonWeapon3Aliases'])
                ->andWhere(['weapon_id' => null])
                ->all(),
            function (SalmonWeapon3 $a, SalmonWeapon3 $b): int {
                $aN = Yii::t('app-weapon3', $a->name);
                $bN = Yii::t('app-weapon3', $b->name);
                return \strnatcasecmp($aN, $bN)
                    ?: \strcmp($aN, $bN)
                    ?: \strnatcasecmp($a->name, $b->name)
                    ?: \strcmp($a->name, $b->name);
            },
        );
    }
}
