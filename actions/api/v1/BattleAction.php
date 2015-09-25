<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use DateTimeZone;
use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;
use app\models\api\v1\PostBattleForm;
use app\models\Battle;
use app\components\helpers\DateTimeFormatter;

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
            
            // 保存時間の読み込みのために再読込する
            $battle = Battle::findOne(['id' => $battle->id]);

            return $this->runGetImpl($battle);
        } catch(\Exception $e) {
            $transaction->rollback();
            return $this->formatError(['system' => [ $e->getMessage() ]], 500);
        }
    }

    private function runGetImpl(Battle $battle)
    {
        $ret = [
            'id' => $battle->id,
            'user' => $battle->user ? $battle->user->toJsonArray() : null,
            'rule' => $battle->rule ? $battle->rule->toJsonArray() : null,
            'map' => $battle->map ? $battle->map->toJsonArray() : null,
            'weapon' => $battle->weapon ? $battle->weapon->toJsonArray() : null,
            'rank' => $battle->rank ? $battle->rank->toJsonArray() : null,
            'level' => $battle->level,
            'result' => $battle->is_win === true ? 'win' : ($battle->is_win === false ? 'lose' : null),
            'rank_in_team' => $battle->rank_in_team,
            'kill' => $battle->kill,
            'death' => $battle->death,
            'agent' => [
                'name' => $battle->agent,
                'version' => $battle->agent_version,
            ],
            'start_at' => $battle->start_at == ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->start_at))
                : null,
            'end_at' => $battle->end_at == ''
                ? DateTimeFormatter::unixTimeToJsonArra(strtotime($battle->end_at))
                : null,
            'register_at' => DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->at)),
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
