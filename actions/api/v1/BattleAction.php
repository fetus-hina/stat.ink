<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v1;

use Throwable;
use Yii;
use app\components\helpers\Blackout;
use app\components\helpers\ImageConverter;
use app\components\jobs\ImageS3Job;
use app\components\jobs\OstatusJob;
use app\components\jobs\SlackJob;
use app\components\web\ServiceUnavailableHttpException;
use app\models\Agent;
use app\models\Battle;
use app\models\BattleDeathReason;
use app\models\User;
use app\models\api\v1\DeleteBattleForm;
use app\models\api\v1\PatchBattleForm;
use app\models\api\v1\PostBattleForm;
use yii\base\DynamicModel;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function array_merge;
use function array_shift;
use function base64_encode;
use function file_get_contents;
use function is_array;
use function is_string;
use function json_encode;
use function sprintf;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        if ($request->isDelete) {
            return $this->runDelete();
        } elseif ($request->isPatch) {
            return $this->runPatch();
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
                    'targetAttribute' => 'screen_name',
                ],
                [['newer_than', 'older_than'], 'integer'],
                [['count'], 'default', 'value' => 10],
                [['count'], 'integer', 'min' => 1, 'max' => 100],
            ],
        );
        if (!$model->validate()) {
            return $this->formatError($model->getErrors(), 400);
        }

        $query = Battle::find()
            ->innerJoinWith('user')
            ->with([
                'agent',
                'agentGameVersion',
                'battleDeathReasons',
                'battleEvents',
                'battleImageGear',
                'battleImageJudge',
                'battleImageResult',
                'battlePlayers',
                'battlePlayers.rank',
                'battlePlayers.weapon',
                'bonus',
                'env',
                'festTitle',
                'festTitleAfter',
                'gender',
                'lobby',
                'map',
                'rank',
                'rank.group',
                'rankAfter',
                'rankAfter.group',
                'rule',
                'rule.mode',
                'splatoonVersion',
                'user',
                'user.env',
                'user.userStat',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
                'weapon.type',
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
            return $list ? $this->runGetImpl(array_shift($list)) : null;
        }

        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        return array_map(
            fn ($model) => $model->toJsonArray(),
            $list,
        );
        // }}}
    }

    private function runPost()
    {
        // {{{
        $request = Yii::$app->getRequest();
        $form = new PostBattleForm();
        $form->attributes = $request->getBodyParams();
        foreach (['image_judge', 'image_result', 'image_gear'] as $key) {
            if ($form->$key == '') {
                $form->$key = UploadedFile::getInstanceByName($key);
            }
        }
        if (!$form->validate()) {
            $this->logError(array_merge(
                $form->getErrors(),
                ['req' => @base64_encode($request->getRawBody())],
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
                $agent,
            );
        }

        if (!$userLock = $form->acquireLock()) {
            throw new ServiceUnavailableHttpException();
        }

        // 重複登録チェックして重複していれば前のレコードを返す
        if ($battle = $form->getSameBattle()) {
            return $this->runGetImpl($battle);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $battle = $this->saveData($form);
            if (!$battle instanceof Battle) {
                return $battle;
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollback();
            $this->logError([
                'system' => [ $e->getMessage() ],
            ]);
            return $this->formatError([
                'system' => [ $e->getMessage() ],
            ], 500);
        }
        unset($userLock);

        // 保存時間の読み込みのために再読込する
        $battle = Battle::findOne(['id' => $battle->id]);

        // バックグラウンドジョブの登録
        // (Slack, Ostatus への push のタスク登録など)
        $this->registerBackgroundJob($battle);

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
            $agent = Agent::findOne([
                'name' => (string)$form->agent,
                'version' => (string)$form->agent_version,
            ]);
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
        if ($image = $form->toImageJudge($battle)) {
            $binary = is_string($form->image_judge)
                ? $form->image_judge
                : file_get_contents($form->image_judge->tempName, false);
            if (
                !ImageConverter::convert(
                    $binary,
                    $imageOutputDir . '/' . $image->filename,
                    false,
                    null, // disable image-archive
                )
            ) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'judge'),
                    ],
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'judge'),
                    ],
                ], 500);
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(judge)'),
                    ],
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(judge)'),
                    ],
                ], 500);
            }
        }
        if ($image = $form->toImageResult($battle)) {
            $binary = is_string($form->image_result)
                ? $form->image_result
                : file_get_contents($form->image_result->tempName, false);

            $blackoutList = [];
            if (
                (1 <= $form->rank_in_team
                && $form->rank_in_team <= 4)
                    && ($form->result === 'win'
                    || $form->result === 'lose')
                    && ($form->lobby != '')
            ) {
                $blackoutList = Blackout::getBlackoutTargetList(
                    $form->lobby,
                    $form->user->blackout,
                    ($form->result === 'win' ? 0 : 4) + $form->rank_in_team,
                );
            }

            if (
                !ImageConverter::convert(
                    $binary,
                    $imageOutputDir . '/' . $image->filename,
                    $blackoutList,
                    null, // disable image-archive
                )
            ) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'result'),
                    ],
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'result'),
                    ],
                ], 500);
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(result)'),
                    ],
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(result)'),
                    ],
                ], 500);
            }
        }
        if ($image = $form->toImageGear($battle)) {
            $binary = is_string($form->image_gear)
                ? $form->image_gear
                : file_get_contents($form->image_gear->tempName, false);
            if (
                !ImageConverter::convert(
                    $binary,
                    $imageOutputDir . '/' . $image->filename,
                    [],
                    null, // disable image-archive
                )
            ) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'gear'),
                    ],
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not convert "{0}" image.', 'gear'),
                    ],
                ], 500);
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(gear)'),
                    ],
                ]);
                return $this->formatError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', 'battle_image(gear)'),
                    ],
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
            'deleted' => $form->deletedIdList,
            'not-deleted' => $form->errorIdList,
        ];
    }

    private function runPatch()
    {
        $request = Yii::$app->getRequest();
        $form = new PatchBattleForm();
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

        if (!$battle = $form->save()) {
            return $this->formatError($form->getErrors(), 400);
        }

        return $this->runGetImpl($battle);
    }

    private function runGetImpl(Battle $battle)
    {
        return $this->runGetImpl2(
            $battle,
            BattleDeathReason::find()
                ->andWhere(['battle_id' => $battle->id])
                ->with(['reason', 'reason.type'])
                ->all(),
            $battle->battlePlayers,
            $battle->agent,
        );
    }

    /**
     * @param BattleDeathReason[] $deathReasons
     */
    private function runGetImpl2(
        Battle $battle,
        array $deathReasons,
        ?array $players = null,
        ?Agent $agent = null,
    ) {
        $ret = $battle->toJsonArray();
        $ret['death_reasons'] = array_map(
            fn ($model): array => $model->toJsonArray(),
            $deathReasons,
        );
        $ret['players'] = is_array($players) && $players
            ? array_map(
                fn ($model): array => $model->toJsonArray(),
                $players,
            )
            : null;
        $ret['agent']['name'] = $agent ? $agent->name : null;
        $ret['agent']['version'] = $agent ? $agent->version : null;

        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        return $ret;
    }

    private function registerBackgroundJob(Battle $battle): void
    {
        $user = $battle->user;

        // Slack 投稿
        if ($user && $user->isSlackIntegrated) {
            Yii::$app->queue->push(new SlackJob([
                'hostInfo' => Yii::$app->getRequest()->getHostInfo(),
                'version' => 1,
                'battle' => $battle->id,
            ]));
        }

        // Ostatus 投稿
        if ($user && $user->isOstatusIntegrated) {
            Yii::$app->queue
                ->priority(OstatusJob::getJobPriority())
                ->push(new OstatusJob([
                    'hostInfo' => Yii::$app->getRequest()->getHostInfo(),
                    'version' => 1,
                    'battle' => $battle->id,
                ]));
        }

        // S3 への画像アップロード
        if (Yii::$app->imgS3->enabled) {
            foreach ($battle->battleImages as $image) {
                Yii::$app->queue
                    ->priority(ImageS3Job::getJobPriority())
                    ->push(new ImageS3Job([
                        'file' => $image->filename,
                    ]));
            }
        }
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
            ['error' => $errors],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
        $text = sprintf(
            'API/Battle Error: RemoteAddr=[%s], Data=%s',
            $_SERVER['REMOTE_ADDR'],
            $output,
        );
        if (isset($errors['system'])) {
            Yii::error($text);
        } else {
            Yii::warning($text);
        }
    }
}
