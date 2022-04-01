<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v2\battle;

use Yii;
use app\components\helpers\ImageConverter;
use app\components\jobs\SlackJob;
use app\components\web\ServiceUnavailableHttpException;
use app\models\Battle2;
use app\models\api\v2\PostBattleForm;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class CreateAction extends BaseAction
{
    public function run()
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
                ['req' => @base64_encode($request->getRawBody())]
            ));
            return $this->formatError($form->getErrors(), 400);
        }

        // テストモード用
        if ($form->isTest) {
            $resp = Yii::$app->getResponse();
            $resp->format = 'json';
            $resp->statusCode = 200;
            return [
                'validate' => true,
            ];
        }

        if (!$userLock = $form->acquireLock()) {
            throw new ServiceUnavailableHttpException();
        }

        // 重複登録チェックして重複していれば前のレコードを返す
        if ($battle = $form->getSameBattle()) {
            return $this->created($battle, true);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $battle = $this->saveData($form);
            if (!$battle instanceof Battle2) {
                return $battle;
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollback();
            $this->logError([
                'system' => [ $e->getMessage() ],
            ]);
            return $this->formatError([
                'system' => [ $e->getMessage() ],
                'stackTrace' => $e->getTraceAsString(),
            ], 500);
        }
        unset($userLock);

        // 保存時間の読み込みのために再読込する
        $battle->refresh();

        // バックグラウンドジョブの登録
        // (Slack への push のタスク登録など)
        $this->registerBackgroundJob($battle);

        return $this->created($battle);
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
            return $this->formatError(
                [
                    'system' => [
                        Yii::t('app', 'Please send meaningful data.'),
                    ],
                ],
                400,
            );
        }

        if (!$battle->save()) {
            $this->logError([
                'system' => [
                    Yii::t('app', 'Could not save to database: {0}', ['battle']),
                ],
                'system_' => $battle->getErrors(),
            ]);
            return $this->formatError(
                [
                    'system' => [
                        Yii::t('app', 'Could not save to database: {0}', ['battle']),
                    ],
                    'system_' => $battle->getErrors(),
                ],
                500,
            );
        }

        if ($events = $form->toEvents($battle)) {
            if (!$events->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save to database: {0}', ['battle_events']),
                    ],
                    'system_' => $battle->getErrors(),
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not save to database: {0}', ['battle_events']),
                        ],
                        'system_' => $battle->getErrors(),
                    ],
                    500,
                );
            }
        }
        if ($json = $form->toSplatnetJson($battle)) {
            if (!$json->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save to database: {0}', ['battle2_splatnet']),
                    ],
                    'system_' => $battle->getErrors(),
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not save to database: {0}', ['battle2_splatnet']),
                        ],
                        'system_' => $battle->getErrors(),
                    ],
                    500,
                );
            }
        }
        foreach ($form->toDeathReasons($battle) as $reason) {
            if ($reason && !$reason->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save to database: {0}', ['battle_death_reason']),
                    ],
                    'system_' => $reason->getErrors(),
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not save to database: {0}', ['battle_death_reason']),
                        ],
                        'system_' => $reason->getErrors(),
                    ],
                    500,
                );
            }
        }
        foreach ($form->toPlayers($battle) as $player) {
            if ($player && !$player->save()) {
                $this->logError([
                    'system' => [
                        'Could not save to database: battle_player',
                    ],
                    'system_' => $player->getErrors(),
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            'Could not save to database: battle_player',
                        ],
                        'system_' => $player->getErrors(),
                    ],
                    500,
                );
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
                        Yii::t('app', 'Could not convert "{0}" image.', ['judge']),
                    ],
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not convert "{0}" image.', ['judge']),
                        ],
                    ],
                    500,
                );
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', ['battle_image(judge)']),
                    ],
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not save {0}', ['battle_image(judge)']),
                        ],
                    ],
                    500,
                );
            }
        }
        if ($image = $form->toImageResult($battle)) {
            $binary = is_string($form->image_result)
                ? $form->image_result
                : file_get_contents($form->image_result->tempName, false);

            $blackoutList = [];
            // if ((1 <= $form->rank_in_team && $form->rank_in_team <= 4) &&
            //         ($form->result === 'win' || $form->result === 'lose') &&
            //         ($form->lobby != '')
            // ) {
            //     $user = Yii::$app->getUser()->getIdentity();
            //     $blackoutList = \app\components\helpers\Blackout::getBlackoutTargetList(
            //         $form->lobby,
            //         $user->blackout,
            //         (($form->result === 'win') ? 0 : 4) + $form->rank_in_team
            //     );
            // }

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
                        Yii::t('app', 'Could not convert "{0}" image.', ['result']),
                    ],
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not convert "{0}" image.', ['result']),
                        ],
                    ],
                    500,
                );
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', ['battle_image(result)']),
                    ],
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not save {0}', ['battle_image(result)']),
                        ],
                    ],
                    500,
                );
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
                        Yii::t('app', 'Could not convert "{0}" image.', ['gear']),
                    ],
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not convert "{0}" image.', ['gear']),
                        ],
                    ],
                    500,
                );
            }
            if (!$image->save()) {
                $this->logError([
                    'system' => [
                        Yii::t('app', 'Could not save {0}', ['battle_image(gear)']),
                    ],
                ]);
                return $this->formatError(
                    [
                        'system' => [
                            Yii::t('app', 'Could not save {0}', ['battle_image(gear)']),
                        ],
                    ],
                    500,
                );
            }
        }

        return $battle;
        // }}}
    }

    private function registerBackgroundJob(Battle2 $battle): void
    {
        $user = $battle->user;

        // Slack 投稿
        if ($user && $user->isSlackIntegrated) {
            Yii::$app->queue
                ->priority(SlackJob::getJobPriority())
                ->push(new SlackJob([
                    'hostInfo' => Yii::$app->getRequest()->getHostInfo(),
                    'version' => 2,
                    'battle' => $battle->id,
                ]));
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

    private function created(Battle2 $battle, bool $found = false)
    {
        $resp = Yii::$app->getResponse();
        $header = $resp->getHeaders();
        $resp->statusCode = 201;
        $resp->statusText = 'Created';
        $resp->format = 'raw';
        $resp->data = '';
        $header->set(
            'Location',
            Url::to(
                ['/show-v2/battle',
                    'screen_name' => $battle->user->screen_name,
                    'battle' => $battle->id,
                ],
                true,
            ),
        );
        $header->set(
            'X-Api-Location',
            Url::to(
                ['/api-v2-battle/view',
                    'id' => $battle->id,
                ],
                true,
            ),
        );
        $header->set('X-User-Screen-Name', $battle->user->screen_name);
        $header->set('X-Battle-ID', (string)$battle->id);
        if ($found) {
            $resp->statusCode = 302;
            $resp->statusText = 'Found';
        }
        return $resp;
    }
}
