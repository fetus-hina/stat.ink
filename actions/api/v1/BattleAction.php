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
use yii\helpers\Url;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\ImageConverter;
use app\models\Agent;
use app\models\Battle;
use app\models\api\v1\PostBattleForm;

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
                $form->$key = UploadedFile::getInstanceByName($key);
            }
        }
        if (!$form->validate()) {
            return $this->formatError($form->getErrors(), 400);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $tmpFiles = [];
        try {
            $battle = $form->toBattle();
            if (!$battle->isMeaningful) {
                $transaction->rollback();
                return $this->formatError([
                    'system' => [ Yii::t('app', 'Please send meaningful data.') ],
                ], 400);
            }
            if ($form->agent != '' || $form->agent_version != '') {
                $agent = Agent::findOne(['name' => (string)$form->agent, 'version' => (string)$form->agent_version]);
                if (!$agent) {
                    $agent = new Agent();
                    $agent->name = (string)$form->agent;
                    $agent->version = (string)$form->agent_version;
                    if (!$agent->save()) {
                        return $this->formatError([
                            'system' => [ Yii::t('app', 'Could not save to database: {0}', 'agent') ],
                            'system_' => $battle->getErrors(),
                        ], 500);
                    }
                }
                $battle->agent_id = $agent->id;
            }
            if (!$battle->save()) {
                $transaction->rollback();
                return $this->formatError([
                    'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle') ],
                    'system_' => $battle->getErrors(),
                ], 500);
            }
            if ($battle->isNawabari) {
                $nawabari = $form->toBattleNawabari($battle);
                if ($nawabari->isMeaningful) {
                    if (!$nawabari->save()) {
                        $transaction->rollback();
                        return $this->formatError([
                            'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle_nawabari') ],
                            'system_' => $nawabari->getErrors(),
                        ], 500);
                    }
                }
            } elseif ($battle->isGachi) {
                $gachi = $form->toBattleGachi($battle);
                if ($gachi->isMeaningful) {
                    if (!$gachi->save()) {
                        $transaction->rollback();
                        return $this->formatError([
                            'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle_ggachi') ],
                            'system_' => $gachi->getErrors(),
                        ], 500);
                    }
                }
            }

            $imageOutputDir = Yii::getAlias('@webroot/images');
            if ($image = $form->toImageJudge($battle)) {
                $binary = is_string($form->image_judge)
                    ? $form->image_judge
                    : file_get_contents($form->image_judge->tempName, false);
                if (!ImageConverter::convert(
                    $binary,
                    $imageOutputDir . '/' . $image->filename,
                    $imageOutputDir . '/' . str_replace('.jpg', '.webp', $image->filename)
                )) {
                    $transaction->rollback();
                    return $this->formatError([
                        'system' => [
                            Yii::t('app', 'Could not convert "{0}" image.', 'judge'),
                        ]
                    ], 500);
                }
                if (!$image->save()) {
                    $transaction->rollback();
                    return $this->formatError([
                        'system' => [
                            Yii::t('app', 'Could not save {0}', 'battle_image(judge)'),
                        ]
                    ], 500);
                }
            }
            if ($image = $form->toImageResult($battle)) {
                $binary = is_string($form->image_result)
                    ? $form->image_result
                    : file_get_contents($form->image_result->tempName, false);
                if (!ImageConverter::convert(
                    $binary,
                    $imageOutputDir . '/' . $image->filename,
                    $imageOutputDir . '/' . str_replace('.jpg', '.webp', $image->filename)
                )) {
                    $transaction->rollback();
                    return $this->formatError([
                        'system' => [
                            Yii::t('app', 'Could not convert "{0}" image.', 'result'),
                        ]
                    ], 500);
                }
                if (!$image->save()) {
                    $transaction->rollback();
                    return $this->formatError([
                        'system' => [
                            Yii::t('app', 'Could not save {0}', 'battle_image(result)'),
                        ]
                    ], 500);
                }
            }

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
            'url' => Url::to(['show/battle',
                'screen_name' => $battle->user->screen_name,
                'battle' => $battle->id,
            ], true),
            'user' => $battle->user ? $battle->user->toJsonArray() : null,
            'rule' => $battle->rule ? $battle->rule->toJsonArray() : null,
            'map' => $battle->map ? $battle->map->toJsonArray() : null,
            'weapon' => $battle->weapon ? $battle->weapon->toJsonArray() : null,
            'rank' => $battle->rank ? $battle->rank->toJsonArray() : null,
            'rank_exp' => $battle->rank_exp,
            'rank_after' => $battle->rankAfter ? $battle->rankAfter->toJsonArray() : null,
            'rank_exp_after' => $battle->rank_exp_after,
            'level' => $battle->level,
            'level_after' => $battle->level_after,
            'cash' => $battle->cash,
            'cash_after' => $battle->cash_after,
            'result' => $battle->is_win === true ? 'win' : ($battle->is_win === false ? 'lose' : null),
            'rank_in_team' => $battle->rank_in_team,
            'kill' => $battle->kill,
            'death' => $battle->death,
            'image_judge' => $battle->battleImageJudge
                ? Url::to(Yii::getAlias('@web/images') . '/' . $battle->battleImageJudge->filename, true)
                : null,
            'image_result' => $battle->battleImageResult
                ? Url::to(Yii::getAlias('@web/images') . '/' . $battle->battleImageResult->filename, true)
                : null,
            'agent' => [
                'name' => $battle->agent ? $battle->agent->name : null,
                'version' => $battle->agent ? $battle->agent->version : null,
            ],
            'start_at' => $battle->start_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->start_at))
                : null,
            'end_at' => $battle->end_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->end_at))
                : null,
            'register_at' => DateTimeFormatter::unixTimeToJsonArray(strtotime($battle->at)),
        ];
        if ($battle->isNawabari) {
            $nawabari = $battle->battleNawabari;
            $ret = array_merge($ret, [
                'my_point' => $nawabari ? (int)$nawabari->my_point : null,
                'my_team_final_point' => $nawabari ? $nawabari->my_team_final_point : null,
                'his_team_final_point' => $nawabari ? $nawabari->his_team_final_point : null,
                'my_team_final_percent' => $nawabari ? $nawabari->my_team_final_percent : null,
                'his_team_final_percent' => $nawabari ? $nawabari->his_team_final_percent : null,
            ]);
        }
        if ($battle->isGachi) {
            $gachi = $battle->battleGachi;
            $ret = array_merge($ret, [
                'knock_out' => $gachi ? $gachi->is_knock_out : null,
                'my_team_count' => $gachi ? $gachi->my_team_count : null,
                'his_team_count' => $gachi ? $gachi->his_team_count : null,
            ]);
        }
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
