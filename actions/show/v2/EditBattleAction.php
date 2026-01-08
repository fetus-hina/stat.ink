<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show\v2;

use Yii;
use app\models\Battle2;
use app\models\Battle2DeleteForm;
use app\models\Battle2Form;
use app\models\Map2;
use app\models\Rank2;
use app\models\Weapon2;
use app\models\WeaponType2;
use yii\base\Action;
use yii\helpers\ArrayHelper;

use function array_merge;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;
use const SORT_NATURAL;

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
                        return $this->controller->redirect([
                            'show-v2/user',
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
                        $this->controller->redirect([
                            'show-v2/battle',
                            'screen_name' => $this->battle->user->screen_name,
                            'battle' => $this->battle->id,
                        ]);
                        return;
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

    /**
     * @return array<string, string>
     */
    private function makeMaps(): array
    {
        return self::arrayMerge(
            ['' => Yii::t('app', 'Unknown')],
            ArrayHelper::asort(
                ArrayHelper::map(
                    Map2::find()->all(),
                    'id',
                    fn (Map2 $map): string => Yii::t('app-map2', $map->name),
                ),
                SORT_NATURAL,
            ),
        );
    }

    /**
     * @return array<string, string|array<string, string>>
     */
    private function makeWeapons(): array
    {
        return array_merge(
            ['' => Yii::t('app', 'Unknown')],
            ArrayHelper::map(
                WeaponType2::find()
                    ->with(['weapons'])
                    ->innerJoinWith(['category'], true)
                    ->orderBy([
                        'weapon_category2.id' => SORT_ASC,
                        'weapon_type2.id' => SORT_ASC,
                    ])
                    ->all(),
                fn (WeaponType2 $type): string => $type->name === $type->category->name
                    ? Yii::t('app-weapon2', $type->category->name)
                    : vsprintf('%s » %s', [
                        Yii::t('app-weapon2', $type->category->name),
                        Yii::t('app-weapon2', $type->name),
                    ]),
                fn (WeaponType2 $type): array => ArrayHelper::asort(
                    ArrayHelper::map(
                        $type->weapons,
                        'key',
                        fn (Weapon2 $weapon): string => Yii::t('app-weapon2', $weapon->name),
                    ),
                    SORT_NATURAL,
                ),
            ),
        );
    }

    /**
     * @return array<string, string>
     */
    private function makeRanks(): array
    {
        return self::arrayMerge(
            ['' => ''],
            ArrayHelper::map(
                Rank2::find()->orderBy(['id' => SORT_DESC])->all(),
                'id',
                fn (Rank2 $rank): string => Yii::t('app-rank2', $rank->name),
            ),
        );
    }

    private static function arrayMerge(array ...$arrays): array
    {
        $ret = [];
        foreach ($arrays as $a) {
            foreach ($a as $k => $v) {
                $ret[$k] = $v;
            }
        }

        return $ret;
    }
}
