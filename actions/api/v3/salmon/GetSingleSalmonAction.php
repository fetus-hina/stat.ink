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
use app\components\helpers\UuidRegexp;
use app\models\Salmon3;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function preg_match;

final class GetSingleSalmonAction extends Action
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

    public function run(string $uuid, bool $full = false): Response
    {
        if (!preg_match(UuidRegexp::get(true), $uuid)) {
            throw new BadRequestHttpException();
        }

        $model = Salmon3::find()
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
                'uuid' => $uuid,
                'is_deleted' => false,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = $model->user;
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $isAuthenticated = Yii::$app->user->isGuest
            ? false
            : (int)$user->id === (int)Yii::$app->user->id;

        $resp = Yii::$app->response;
        $resp->data = SalmonApiFormatter::toJson(
            $model,
            $isAuthenticated,
            $full,
        );

        return $resp;
    }
}
