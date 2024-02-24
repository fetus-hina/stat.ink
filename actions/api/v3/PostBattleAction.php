<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\BattleApiFormatter;
use app\components\jobs\SlackJob;
use app\models\Battle3;
use app\models\api\v3\PostBattleForm;
use app\models\api\v3\postBattle\PlayerForm;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

use function array_map;
use function is_array;
use function json_encode;

final class PostBattleAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit();
    }

    public function run(): Response
    {
        $form = Yii::createObject(PostBattleForm::class);
        $form->attributes = Yii::$app->request->getBodyParams();
        foreach (['image_judge', 'image_result', 'image_gear'] as $key) {
            if (!$form->$key) {
                $form->$key = UploadedFile::getInstanceByName($key);
            }
        }

        $battle = $form->save();
        if (!$battle) {
            if ($form->hasErrors('weapon')) {
                Yii::warning(
                    'Weapon Error: ' . json_encode($form->getErrors('weapon')) . ' ' .
                        json_encode($form->weapon),
                    __METHOD__,
                );
            }

            $attrs = ['our_team_players', 'their_team_players', 'third_team_players'];
            foreach ($attrs as $attr) {
                if (is_array($form->$attr) && $form->$attr) {
                    foreach ($form->$attr as $player) {
                        $playerModel = Yii::createObject(PlayerForm::class);
                        $playerModel->attributes = $player;
                        if (!$playerModel->validate() && $playerModel->hasErrors('weapon')) {
                            Yii::warning(
                                'Player Weapon Error: ' .
                                    json_encode($playerModel->getErrors('weapon')) . ' ' .
                                    json_encode($playerModel->weapon),
                                __METHOD__,
                            );
                        }
                    }
                }
            }

            return $this->formatError($form->getFirstErrors(), 400);
        } elseif ($battle === true) {
            return $this->formatError(null, 200); // validation OK
        }

        // 保存時間の読み込みのために再読込する
        $uuid = $battle->uuid;
        $battle = Battle3::find()
            ->with(
                ArrayHelper::toFlatten([
                    [
                        'battlePlayer3s',
                        'battlePlayer3s.crown',
                        'battlePlayer3s.species',
                        'battlePlayer3s.splashtagTitle',
                        'battlePlayer3s.weapon',
                        'battlePlayer3s.weapon.canonical',
                        'battlePlayer3s.weapon.mainweapon',
                        'battlePlayer3s.weapon.mainweapon.type',
                        'battlePlayer3s.weapon.special',
                        'battlePlayer3s.weapon.subweapon',
                        'battlePlayer3s.weapon.weapon3Aliases',
                    ],
                    array_map(
                        fn (string $base): array => [
                            "battlePlayer3s.{$base}",
                            "battlePlayer3s.{$base}.ability",
                            "battlePlayer3s.{$base}.gearConfigurationSecondary3s",
                            "battlePlayer3s.{$base}.gearConfigurationSecondary3s.ability",
                        ],
                        ['clothing', 'headgear', 'shoes'],
                    ),
                ]),
            )
            ->andWhere(['uuid' => $uuid])
            ->limit(1)
            ->one();

        // バックグラウンドジョブの登録
        // (Slack への push のタスク登録など)
        if ($form->isCreated) {
            $this->registerBackgroundJob($battle);
        }

        return $this->created($battle, (bool)$form->isCreated);
    }

    private function created(Battle3 $battle, bool $isCreated): Response
    {
        $resp = Yii::$app->response;
        $resp->statusCode = 201;
        $resp->headers->fromArray([
            'Location' => Url::to(
                ['/show-v3/battle',
                    'screen_name' => $battle->user->screen_name,
                    'battle' => $battle->uuid,
                ],
                true,
            ),
            'X-Api-Location' => Url::to(
                ['/api-v3/single-battle', 'uuid' => $battle->uuid],
                true,
            ),
            'X-User-Screen-Name' => $battle->user->screen_name,
            'X-Battle-ID' => $battle->uuid,
            'X-Found' => $isCreated ? '?0' : '?1',
        ]);
        $resp->data = BattleApiFormatter::toJson($battle, true, false);
        return $resp;
    }

    private function formatError(?array $errors, int $code): Response
    {
        $resp = Yii::$app->response;
        $resp->statusCode = $code;
        $resp->data = ['error' => $errors];
        return $resp;
    }

    private function registerBackgroundJob(Battle3 $battle): void
    {
        $user = $battle->user;
        if (!$user) {
            return;
        }

        // Slack 投稿
        if ($user->isSlackIntegrated) {
            Yii::$app->queue
                ->priority(SlackJob::getJobPriority())
                ->push(new SlackJob([
                    'hostInfo' => Yii::$app->getRequest()->getHostInfo(),
                    'version' => 3,
                    'battle' => $battle->id,
                ]));
        }
    }
}
