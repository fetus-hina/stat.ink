<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show\v2;

use Yii;
use app\components\helpers\T;
use app\models\Battle2;
use app\models\Battle2DeleteForm;
use app\models\Battle2Form;
use app\models\Map2;
use app\models\Rank2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use const SORT_ASC;
use const SORT_DESC;

/**
 * @property-read bool $isEditable
 */
final class EditBattleAction extends Action
{
    private $battle;

    public function init()
    {
        parent::init();
        $this->battle = null;
        if ($user = Yii::$app->user->identity) {
            $this->battle = Battle2::findOne([
                'id' => Yii::$app->request->get('battle'),
                'user_id' => $user->id,
            ]);
        }
    }

    public function getIsEditable()
    {
        return !!$this->battle;
    }

    /**
     * @return Response|string
     */
    public function run()
    {
        $del = Yii::createObject(Battle2DeleteForm::class);
        if (Yii::$app->request->isPost) {
            $form = Yii::createObject(['class' => Battle2Form::class]);
            $form->load($_POST);
            $del->load($_POST);
            $del->battle = $this->battle->id;
            if (Yii::$app->request->post('_action') === 'delete') {
                if ($del->validate()) {
                    $transaction = Yii::$app->db->beginTransaction();
                    if ($del->delete()) {
                        $transaction->commit();
                        return T::webController($this->controller)
                            ->redirect(['show-v2/user',
                                'screen_name' => $this->battle->user->screen_name,
                            ]);
                    }
                    $transaction->rollback();
                }
            } else {
                if ($form->validate()) {
                    $this->battle->attributes = $form->attributes;
                    $this->battle->is_win = $form->getIsWin();
                    if ($this->battle->save()) {
                        return T::webController($this->controller)
                            ->redirect([
                                'show-v2/battle',
                                'screen_name' => $this->battle->user->screen_name,
                                'battle' => $this->battle->id,
                            ]);
                    }
                }
            }
        } else {
            $form = Battle2Form::fromBattle($this->battle);
        }
        return $this->controller->render('edit-battle', [
            'user' => $this->battle->user,
            'battle' => $this->battle,
            'form' => $form,
            'delete' => $del,
            'maps' => $this->makeMaps(),
            'weapons' => $this->makeWeapons(),
            'ranks' => $this->makeRanks(),
        ]);
    }

    private function makeMaps()
    {
        $ret = [];
        foreach (Map2::find()->all() as $map) {
            $ret[$map->id] = Yii::t('app-map2', $map->name);
        }
        asort($ret);
        return static::arrayMerge(
            ['' => Yii::t('app', 'Unknown')],
            $ret
        );
    }

    private function makeWeapons()
    {
        $ret = [];
        $categories = WeaponCategory2::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        foreach ($categories as $category) {
            $types = WeaponType2::find()
                ->andWhere(['category_id' => $category->id])
                ->orderBy(['id' => SORT_ASC])
                ->all();
            foreach ($types as $type) {
                $typeName = $category->name === $type->name
                    ? Yii::t('app-weapon2', $category->name)
                    : sprintf(
                        '%s » %s',
                        Yii::t('app-weapon2', $category->name),
                        Yii::t('app-weapon2', $type->name)
                    );
                $ret[$typeName] = (function (WeaponType2 $type): array {
                    $tmp = [];
                    foreach ($type->weapons as $weapon) {
                        $tmp[$weapon->id] = Yii::t('app-weapon2', $weapon->name);
                    }
                    asort($tmp);
                    return $tmp;
                })($type);
            }
        }
        return static::arrayMerge(
            ['' => Yii::t('app', 'Unknown')],
            $ret
        );
    }

    private function makeRanks()
    {
        return static::arrayMerge(
            ['' => ''],
            ArrayHelper::map(
                Rank2::find()->orderBy(['[[id]]' => SORT_DESC])->asArray()->all(),
                'id',
                fn (array $row): string => Yii::t('app-rank2', $row['name'])
            )
        );
    }

    private static function arrayMerge()
    {
        $ret = [];
        foreach (func_get_args() as $arg) {
            foreach ($arg as $k => $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
}
