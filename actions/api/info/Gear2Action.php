<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\info;

use Yii;
use app\models\Gear2;
use app\models\GearType;
use app\models\Language;
use yii\base\Action;
use yii\web\NotFoundHttpException;

final class Gear2Action extends Action
{
    public $type;

    public function getType(): GearType
    {
        return GearType::findOne(['key' => (string)$this->type]);
    }

    public function run()
    {
        if (!$type = $this->getType()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $gears = Gear2::find()
            ->with([
                'ability',
                'brand',
            ])
            ->andWhere(['type_id' => $type->id])
            ->all();
        \usort($gears, fn (Gear2 $a, Gear2 $b): int => \strnatcasecmp(
            $a->translatedName,
            $b->translatedName,
        ));
        $langs = Language::find()->standard()->all();
        $sysLang = Yii::$app->language;
        usort($langs, function (Language $a, Language $b) use ($sysLang): int {
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
