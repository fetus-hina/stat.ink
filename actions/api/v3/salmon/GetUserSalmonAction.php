<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3\salmon;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\SalmonApiFormatter;
use app\models\Salmon3;
use app\models\Salmon3FilterForm;
use app\models\User;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_map;
use function hash_hmac;
use function http_build_query;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const SORT_DESC;

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

        $query = Salmon3::find()
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
                'salmonPlayer3s.species',
                'salmonPlayer3s.splashtagTitle',
                'salmonPlayer3s.uniform',
                'salmonPlayer3s.uniform.salmonUniform3Aliases',
                'salmonWave3s.event.salmonEvent3Aliases',
                'salmonWave3s.salmonSpecialUse3s.special',
                'salmonWave3s.tide',
                'schedule',
                'schedule.king',
                'schedule.king.salmonKing3Aliases',
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
            ]);

        $filter = Yii::createObject(Salmon3FilterForm::class);
        $filter->load($_GET);
        $filter->validate();
        $filter->decorateQuery($query);

        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query,
            'sort' => false,
        ]);

        $isAuthenticated = Yii::$app->user->isGuest
            ? false
            : (int)$user->id === (int)Yii::$app->user->id;

        $resp = Yii::$app->response;
        $resp->headers->set('Access-Control-Allow-Origin', '*');
        $resp->data = array_map(
            fn (Salmon3 $model): JsExpression => new JsExpression(
                Yii::$app->cache->getOrSet(
                    hash_hmac(
                        'sha256',
                        http_build_query([
                            'full' => $full ? 'yes' : 'no',
                            'id' => (string)$model->id,
                            'isAuthenticated' => $isAuthenticated ? 'yes' : 'no',
                        ]),
                        __FILE__,
                    ),
                    fn (): string => Json::encode(
                        SalmonApiFormatter::toJson(
                            $model,
                            $isAuthenticated,
                            $full,
                        ),
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
                    ),
                    86400,
                ),
            ),
            $dataProvider->getModels(),
        );

        return $resp;
    }
}
