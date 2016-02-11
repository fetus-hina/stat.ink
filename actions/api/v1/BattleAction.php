<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use DateTimeZone;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\Url;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\ImageConverter;
use app\models\Agent;
use app\models\Battle;
use app\models\User;
use app\models\api\v1\PostBattleForm;
use app\models\api\v1\DeleteBattleForm;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        if ($request->isDelete) {
            return $this->runDelete();
        } elseif ($request->isPost) {
            return $this->runPost();
        } elseif ($request->isGet || $request->isHead) {
            return $this->runGet();
        } else {
            throw new MethodNotAllowedHttpException();
        }
    }

    private function runGet()
    {
        // {{{
        $request = Yii::$app->getRequest();
        $model = DynamicModel::validateData(
            [
                'id' => $request->get('id'),
                'screen_name' => $request->get('screen_name'),
                'count' => $request->get('count'),
                'newer_than' => $request->get('newer_than'),
                'older_than' => $request->get('older_than'),
            ],
            [
                [['id'], 'exist',
                    'targetClass' => Battle::className(),
                    'targetAttribute' => 'id',
                ],
                [['screen_name'], 'exist',
                    'targetClass' => User::className(),
                    'targetAttribute' => 'screen_name' ],
                [['newer_than', 'older_than'], 'integer'],
                [['count'], 'default', 'value' => 10],
                [['count'], 'integer', 'min' => 1, 'max' => 100],
            ]
        );
        if (!$model->validate()) {
            return $this->formatError($model->getErrors(), 400);
        }

        $query = Battle::find()
            ->innerJoinWith('user')
            ->with([
                'user',
                'user.userStat',
                'lobby',
                'rule',
                'map',
                'weapon',
                'weapon.subweapon',
                'weapon.special',
                'rank',
                'battleImageResult',
                'battleImageJudge',
                'battleEvents',
            ])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit((int)$model->count);

        foreach (['headgear', 'clothing', 'shoes'] as $gearKey) {
            $query->with([
                "{$gearKey}",
                "{$gearKey}.primaryAbility",
                "{$gearKey}.gear",
                "{$gearKey}.gear.brand",
                "{$gearKey}.gear.brand.strength",
                "{$gearKey}.gear.brand.weakness",
                "{$gearKey}.secondaries",
                "{$gearKey}.secondaries.ability",
            ]);
        }

        if ($model->id != '') {
            $query->andWhere(['{{battle}}.[[id]]' => $model->id]);
        }
        if ($model->screen_name != '') {
            $query->andWhere(['{{user}}.[[screen_name]]' => $model->screen_name]);
        }
        if ($model->newer_than > 0) {
            $query->andWhere(['>', '{{battle}}.[[id]]', $model->newer_than]);
        }
        if ($model->older_than > 0) {
            $query->andWhere(['<', '{{battle}}.[[id]]', $model->older_than]);
        }

        $list = $query->all();
        if ($model->id != '') {
            return empty($list) ? null : $this->runGetImpl(array_shift($list));
        }

        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        return array_map(
            function ($model) {
                return $model->toJsonArray();
            },
            $list
        );
        // }}}
    }

    private function runPost()
    {
        // {{{
        $request = Yii::$app->getRequest();
        $form = new PostBattleForm();
        $form->attributes = $request->getBodyParams();
        foreach (['image_judge', 'image_result'] as $key) {
            if ($form->$key == '') {
                $form->$key = UploadedFile::getInstanceByName($key);
            }
        }
        if (!$form->validate()) {
            $this->logError(array_merge(
                $form->getErrors(),
                ['req' => @base64_encode($request->getRawBody())]
            ));
            return $this->formatError($form->getErrors(), 400);
        }

        // テストモード用
        if ($form->isTest) {
            // validate のみなら既に validate は完了しているので適当なレスポンスボディを返して終わり
            if ($form->test === 'validate') {
                $resp = Yii::$app->getResponse();
                $resp->format = 'json';
                $resp->statusCode = 200;
                return [
                    'validate' => true,
                ];
            }
    
            // dry_run
            // 整形用のダミーデータを準備
            $battle = $form->toBattle();
            $battle->validate();

            $deathReasons = [];
            foreach ($form->toDeathReasons($battle) as $reason) {
                if ($reason) {
                    $deathReasons[] = $reason;
                }
            }
            $players = [];
            foreach ($form->toPlayers($battle) as $player) {
                if ($player) {
                    $players[] = $player;
                }
            }
            $agent = null;
            if ($form->agent != '' || $form->agent_version != '') {
                $agent = new Agent();
                $agent->name = (string)$form->agent;
                $agent->version = (string)$form->agent_version;
            }
            return $this->runGetImpl2(
                $battle,
                $deathReasons,
                $players,
                $agent
            );
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $battle = $this->saveData($form);
            if (!$battle instanceof Battle) {
                return $battle;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            $this->logError([
                'system' => [ $e->getMessage() ],
            ]);
            return $this->formatError([
                'system' => [ $e->getMessage() ],
            ], 500);
        }

        // 保存時間の読み込みのために再読込する
        $battle = Battle::findOne(['id' => $battle->id]);
        return $this->runGetImpl($battle);
        // }}}
    }

    private function saveData(PostBattleForm $form)
    {
        // {{{
        $battle = $form->toBattle();
        if (!$battle->isMeaningful) {
            $this->logError([
                'system' => [ Yii::t('app', 'Please send meaningful data.') ],
            ]);
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
                    $this->logError([
                        'system' => [ Yii::t('app', 'Could not save to database: {0}', 'agent') ],
                        'system_' => $battle->getErrors(),
                    ]);
                    return $this->formatError([
                        'system' => [ Yii::t('app', 'Could not save to database: {0}', 'agent') ],
                        'system_' => $battle->getErrors(),
                    ], 500);
                }
            }
            $battle->agent_id = $agent->id;
        }
        if (!$battle->save()) {
            $this->logError([
                'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle') ],
                'system_' => $battle->getErrors(),
            ]);
            return $this->formatError([
                'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle') ],
                'system_' => $battle->getErrors(),
            ], 500);
        }
        if ($events = $form->toEvents($battle)) {
            if (!$events->save()) {
                $this->logError([
                    'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle_events') ],
                    'system_' => $battle->getErrors(),
                ]);
                return $this->formatError([
                    'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle_events') ],
                    'system_' => $battle->getErrors(),
                ], 500);
            }
        }
        foreach ($form->toDeathReasons($battle) as $reason) {
            if ($reason && !$reason->save()) {
                $this->logError([
                    'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle_death_reason') ],
                    'system_' => $reason->getErrors(),
                ]);
                return $this->formatError([
                    'system' => [ Yii::t('app', 'Could not save to database: {0}', 'battle_death_reason') ],
                    'system_' => $reason->getErrors(),
                ], 500);
            }
        }
        foreach ($form->toPlayers($battle) as $player) {
            if ($player && !$player->save()) {
                $this->logError([
                    'system' => [ 'Could not save to database: battle_player' ],
                    'system_' => $player->getErrors(),
                ]);
                return $this->formatError([
                    'system' => [ 'Could not save to database: battle_player' ],
                    'system_' => $player->getErrors(),
                ], 500);
            }
        }
        $imageOutputDir = Yii::getAlias('@webroot/images');
        $imageArchiveOutputDir = Yii::$app->params['amazonS3'] && Yii::$app->params['amazonS3'][0]['bucket'] != ''
            ? (Yii::getAlias('@app/runtime/image-archive/queue') . '/' . gmdate('Ymd', time() + 9 * 3600)) // JST
            : null;
        if ($image = $form->toImageJudge($battle)) {
            $binary = is_string($form->image_judge)
                ? $form->image_judge
                : file_get_contents($form->image_judge->tempName, false);
            if (!ImageConverter::convert(
                $binary,
                $imageOutputDir . '/' . $image->filename,
                false,
                ($imageArchiveOutputDir
                    ? ($imageArchiveOutputDir . '/' . sprintf('%d-judge.png', $battle->id))
                    : null)
            )) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'judge'),
                    ]
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'judge'),
                    ]
                ], 500);
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(judge)'),
                    ]
                ]);
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
            $myPosition = false;
            if ((1 <= $form->rank_in_team && $form->rank_in_team <= 4) &&
                    ($form->result === 'win' || $form->result === 'lose')
            ) {
                $myPosition = (($form->result === 'win') ? 0 : 4) + $form->rank_in_team;
            }
            if (!ImageConverter::convert(
                $binary,
                $imageOutputDir . '/' . $image->filename,
                ($form->user->is_black_out_others) ? $myPosition : false,
                $imageArchiveOutputDir
                ? ($imageArchiveOutputDir . '/' . sprintf('%d-result.png', $battle->id))
                : null
            )) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'result'),
                    ]
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'result'),
                    ]
                ], 500);
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(result)'),
                    ]
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(result)'),
                    ]
                ], 500);
            }
        }

        return $battle;
        // }}}
    }

    private function runDelete()
    {
        $request = Yii::$app->getRequest();
        $form = new DeleteBattleForm();
        $form->attributes = $request->getBodyParams();
        if (!$form->validate()) {
            return $this->formatError($form->getErrors(), 400);
        }

        // テストモード用
        // validate のみなら既に validate は完了しているので適当なレスポンスボディを返して終わり
        if ($form->test === 'validate') {
            $resp = Yii::$app->getResponse();
            $resp->format = 'json';
            $resp->statusCode = 200;
            return [
                'validate' => true,
            ];
        }

        if (!$form->save()) {
            return $this->formatError($form->getErrors(), 400);
        }

        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = 200;
        return [
            'deleted'       => $form->deletedIdList,
            'not-deleted'   => $form->errorIdList,
        ];
    }

    private function runGetImpl(Battle $battle)
    {
        return $this->runGetImpl2(
            $battle,
            $battle->getBattleDeathReasons()->with(['reason', 'reason.type'])->all(),
            $battle->battlePlayers,
            $battle->agent
        );
    }

    private function runGetImpl2(Battle $battle, array $deathReasons, array $players = null, Agent $agent = null)
    {
        $ret = $battle->toJsonArray();
        $ret['death_reasons'] = array_map(
            function ($model) {
                return $model->toJsonArray();
            },
            $deathReasons
        );
        $ret['players'] = is_array($players) && !empty($players)
            ? array_map(
                function ($model) {
                    return $model->toJsonArray();
                },
                $players
            )
            : null;
        $ret['agent']['name'] = $agent ? $agent->name : null;
        $ret['agent']['version'] = $agent ? $agent->version : null;

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

    private function logError(array $errors)
    {
        $output = json_encode(
            [ 'error' => $errors ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        $text = sprintf(
            'API/Battle Error: RemoteAddr=[%s], Data=%s',
            $_SERVER['REMOTE_ADDR'],
            $output
        );
        if (isset($errors['system'])) {
            Yii::error($text);
        } else {
            Yii::warning($text);
        }
    }
}
