<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3\salmon;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\SalmonApiFormatter;
use app\models\Salmon3;
use app\models\api\v3\PostSalmonForm;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;

use function rawurlencode;
use function vsprintf;

final class PostSalmonAction extends Action
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
        $form = Yii::createObject(PostSalmonForm::class);
        $form->attributes = Yii::$app->request->getBodyParams();

        $battle = $form->save();
        if (!$battle) {
            return $this->formatError($form->getFirstErrors(), 400);
        } elseif ($battle === true) {
            return $this->formatError(null, 200); // validation OK
        }

        // 保存時間の読み込みのために再読込する
        $uuid = $battle->uuid;
        $battle = Salmon3::find()
            ->with([
                'salmonBossAppearance3s.boss',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon.salmonWeapon3Aliases',
                'salmonPlayer3s.special',
                'salmonPlayer3s.splashtagTitle',
                'salmonPlayer3s.uniform',
                'salmonWave3s.event',
                'salmonWave3s.salmonSpecialUse3s.special',
                'salmonWave3s.tide',
                'variables',
            ])
            ->andWhere(['uuid' => $uuid])
            ->limit(1)
            ->one();

        // バックグラウンドジョブの登録
        // (Slack への push のタスク登録など)
        $this->registerBackgroundJob($battle);

        return $this->created($battle);
    }

    private function created(Salmon3 $battle): Response
    {
        $resp = Yii::$app->response;
        $resp->statusCode = 201;
        $resp->headers->fromArray([
            'Location' => Url::to(
                ['/salmon-v3/view',
                    'screen_name' => $battle->user->screen_name,
                    'battle' => $battle->uuid,
                ],
                true,
            ),
            'X-Api-Location' => Url::to(
                vsprintf('@web/api/v3/salmon/%s', [
                    rawurlencode($battle->uuid),
                ]),
                true,
            ),
            'X-User-Screen-Name' => $battle->user->screen_name,
            'X-Battle-ID' => $battle->uuid,
        ]);
        $resp->data = SalmonApiFormatter::toJson($battle, true, false);
        return $resp;
    }

    private function formatError(?array $errors, int $code): Response
    {
        $resp = Yii::$app->response;
        $resp->statusCode = $code;
        $resp->data = ['error' => $errors];
        return $resp;
    }

    private function registerBackgroundJob(Salmon3 $battle): void
    {
        $user = $battle->user;
        if (!$user) {
            return;
        }

        // Slack 投稿
        // if ($user->isSlackIntegrated) {
        //     Yii::$app->queue
        //         ->priority(SlackJob::getJobPriority())
        //         ->push(new SlackJob([
        //             'hostInfo' => Yii::$app->getRequest()->getHostInfo(),
        //             'version' => 3,
        //             'battle' => $battle->id,
        //         ]));
        // }
    }
}
