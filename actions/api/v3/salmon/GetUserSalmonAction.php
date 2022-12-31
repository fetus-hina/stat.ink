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
use app\models\User;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class GetUserSalmonAction extends Action
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

    public function run(string $screen_name, bool $full = false): Response
    {
        $user = User::find()
            ->andWhere(['screen_name' => $screen_name])
            ->limit(1)
            ->one();
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $models = Salmon3::find()
            ->with([
                'agent',
                'bigStage.map3Aliases',
                'bosses.salmonBoss3Aliases',
                'failReason',
                'kingSalmonid.salmonKing3Aliases',
                'salmonBossAppearance3s',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon.salmonWeapon3Aliases',
                'salmonPlayer3s.special',
                'salmonPlayer3s.splashtagTitle',
                'salmonPlayer3s.uniform',
                'salmonWave3s.event.salmonEvent3Aliases',
                'salmonWave3s.salmonSpecialUse3s.special',
                'salmonWave3s.tide',
                'schedule',
                'stage.salmonMap3Aliases',
                'titleAfter.salmonTitle3Aliases',
                'titleBefore.salmonTitle3Aliases',
                'user',
                'variables',
                'version',
            ])
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->orderBy([
                'start_at' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->limit(100)
            ->all();

        $isAuthenticated = Yii::$app->user->isGuest
            ? false
            : (int)$user->id === (int)Yii::$app->user->id;

        $resp = Yii::$app->response;
        $resp->data = \array_map(
            fn (Salmon3 $model): JsExpression => new JsExpression(
                Json::encode(
                    SalmonApiFormatter::toJson(
                        $model,
                        $isAuthenticated,
                        $full,
                    ),
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
                ),
            ),
            $models,
        );

        return $resp;
    }
}
