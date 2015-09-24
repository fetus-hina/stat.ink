<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;
use app\models\api\v1\PostBattleForm;
use app\models\Battle;
use app\models\Map;
use app\models\Rank;
use app\models\Rule;
use app\models\Weapon;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        if ($request->isPost) {
            return $this->runPost();
        }
        
        $id = $request->get('id');
        if (is_scalar($id) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
            if ($battle = Battle::findOne(['id' => $id])) {
                return $this->runGetImpl($battle);
            } else {
                return $this->formatError(['id' => [ 'not found']], 404);
            }
        }
        return $this->formatError(['id' => [ 'bad request' ]], 400);
    }

    private function runPost()
    {
        $request = Yii::$app->getRequest();
        $form = new PostBattleForm();
        $form->attributes = $request->getBodyParams();
        foreach (['image_judge', 'image_result'] as $key) {
            if ($form->$key == '') {
                $form->$key = UploadedFile::getInstance($form, $key);
            }
        }
        if (!$form->validate()) {
            return $this->formatError($form->getErrors(), 400);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $battle = $form->toBattle();
            if (!$battle->isMeaningful) {
                $transaction->rollback();
                return $this->formatError(['system' => [ 'データの送信量が足りません' ]], 400);
            }
            if (!$battle->save()) {
                $transaction->rollback();
                return $this->formatError([
                    'system' => [ '保存失敗 : battle' ],
                    'system_' => $battle->getErrors(),
                ]);
            }
            if ($battle->isNawabari) {
                $nawabari = $form->toBattleNawabari($battle);
                if ($nawabari->isMeaningful) {
                    if (!$nawabari->save()) {
                        $transaction->rollback();
                        return $this->formatError([
                            'system' => [ '保存失敗 : battle_nawabari' ],
                            'system_' => $nawabari->getErrors(),
                        ]);
                    }
                }
            } elseif ($battle->isGachi) {
                $gachi = $form->toBattleGachi($battle);
                if ($gachi->isMeaningful) {
                    if (!$gachi->save()) {
                        $transaction->rollback();
                        return $this->formatError([
                            'system' => [ '保存失敗 : battle_gachi' ],
                            'system_' => $gachi->getErrors(),
                        ]);
                    }
                }
            }
            // TODO: 画像
            $transaction->commit();
            return $this->runGetImpl($battle);
        } catch(\Exception $e) {
            $transaction->rollback();
            return $this->formatError(['system' => [ $e->getMessage() ]], 500);
        }
    }

    private function runGetImpl(Battle $battle)
    {
        $ret = [
            'id'        => $battle->id,
            'rule'      => Rule::safeFindById($battle->rule_id)->key,
            'map'       => Map::safeFindById($battle->map_id)->key,
            'weapon'    => Weapon::safeFindById($battle->weapon_id)->key,
            'rank'      => Rank::safeFindById($battle->rank_id)->key,
            'level'     => $battle->level,
            'result'    => $battle->is_win === true ? 'win' : ($battle->is_win === false ? 'lose' : null),
            'rank_in_team' => $battle->rank_in_team,
            'kill'      => $battle->kill,
            'death'     => $battle->death,
        ];
        // TODO: 画像
        // TODO: ナワバリ
        // TODO: ガチ
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        return $ret;
    }

    private function formatError(array $errors, $code)
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = (int)$code;
        return [
            'error' => $errors,
        ];
    }
}
