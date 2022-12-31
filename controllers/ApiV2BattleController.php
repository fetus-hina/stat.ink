<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use Yii;
use app\components\filters\auth\RequestBodyAuth;
use app\models\Battle2;
use app\models\User;
use yii\base\DynamicModel;
use yii\filters\ContentNegotiator;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class ApiV2BattleController extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
        parent::init();
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::class,
                    ['class' => RequestBodyAuth::class, 'tokenParam' => 'apikey'],
                ],
                'except' => [
                    'options',
                    'postable-options',
                ],
                'optional' => [
                    'index',
                    'index-with-auth',
                    'view',
                ],
            ],
        ]);
    }

    protected function verbs()
    {
        return [
            'index'   => ['GET', 'HEAD'],
            'index-with-auth' => ['GET', 'HEAD'],
            'view'    => ['GET', 'HEAD'],
            'create'  => ['POST'],
            'options' => ['OPTIONS'],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\v2\battle';
        return [
            'create' => [
                'class' => $prefix . '\CreateAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $req = Yii::$app->request;
        $params = [
            'screen_name' => $req->get('screen_name'),
            'only' => $req->get('only'),
            'newer_than' => $req->get('newer_than'),
            'older_than' => $req->get('older_than'),
            'order' => $req->get('order'),
            'count' => $req->get('count'),
        ];
        $model = DynamicModel::validateData($params, [
            [['screen_name'], 'required',
                'when' => fn ($model) => ($model->only === 'splatnet_number') ||
                        ($model->order === 'splatnet_asc') ||
                        ($model->order === 'splatnet_desc'),
            ],
            [['screen_name'], 'string'],
            [['screen_name'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['screen_name' => 'screen_name'],
            ],
            [['only'], 'string'],
            [['only'], 'in', 'range' => [
                'splatnet_number',
            ]],
            [['newer_than', 'older_than'], 'integer', 'min' => 1],
            [['order'], 'string'],
            [['order'], 'in', 'range' => [
                'asc',
                'desc',
                'splatnet_asc',
                'splatnet_desc',
            ]],
            [['count'], 'integer', 'min' => 1, 'max' => 50,
                'when' => fn ($model): bool => $model->only !== 'splatnet_number',
            ],
            [['count'], 'integer', 'min' => 1, 'max' => 1000,
                'when' => fn ($model): bool => $model->only === 'splatnet_number',
            ],
        ]);
        if ($model->hasErrors()) {
            $res = Yii::$app->response;
            $res->format = 'json';
            $res->statusCode = 400;
            return $model->getErrors();
        }
        return $this->createList(
            $model->screen_name != ''
                ? User::findOne(['screen_name' => $model->screen_name])
                : null,
            $model,
            $req->get('format') === 'pretty' ? 'pretty' : null,
        );
    }

    public function actionIndexWithAuth()
    {
        if (!Yii::$app->user->identity) {
            $res = Yii::$app->response;
            $res->format = 'json';
            throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
        }

        $req = Yii::$app->request;
        $params = [
            'only' => $req->get('only'),
            'newer_than' => $req->get('newer_than'),
            'older_than' => $req->get('older_than'),
            'order' => $req->get('order'),
            'count' => $req->get('count'),
        ];
        $model = DynamicModel::validateData($params, [
            [['only'], 'string'],
            [['only'], 'in', 'range' => [
                'splatnet_number',
            ]],
            [['newer_than', 'older_than'], 'integer', 'min' => 1],
            [['order'], 'string'],
            [['order'], 'in', 'range' => [
                'asc',
                'desc',
                'splatnet_asc',
                'splatnet_desc',
            ]],
            [['count'], 'integer', 'min' => 1, 'max' => 50,
                'when' => fn ($model): bool => $model->only !== 'splatnet_number',
            ],
            [['count'], 'integer', 'min' => 1, 'max' => 1000,
                'when' => fn ($model): bool => $model->only === 'splatnet_number',
            ],
        ]);
        if ($model->hasErrors()) {
            $res = Yii::$app->response;
            $res->format = 'json';
            $res->statusCode = 400;
            return $model->getErrors();
        }
        return $this->createList(
            Yii::$app->user->identity,
            $model,
            $req->get('format') === 'pretty' ? 'pretty' : null,
        );
    }

    private function createList(?User $user, DynamicModel $model, ?string $format = null): array
    {
        $query = Battle2::find()
            ->orderBy(['battle2.id' => SORT_DESC])
            ->limit(10);

        if ($model->only === 'splatnet_number') {
            $query
                ->andWhere(['not', ['{{battle2}}.[[splatnet_number]]' => null]])
                ->orderBy(['{{battle2}}.[[splatnet_number]]' => SORT_DESC]);
        } else {
            $query->withFreshness()
                ->with([
                    // {{{
                    'agent',
                    'agentGameVersion',
                    'battleDeathReasons',
                    'battleImageGear',
                    'battleImageJudge',
                    'battleImageResult',
                    'battlePlayers',
                    'battlePlayers.festTitle',
                    'battlePlayers.gender',
                    'battlePlayers.rank',
                    'battlePlayers.rank.group',
                    'battlePlayers.weapon',
                    'battlePlayers.weapon.canonical',
                    'battlePlayers.weapon.mainReference',
                    'battlePlayers.weapon.special',
                    'battlePlayers.weapon.subweapon',
                    'battlePlayers.weapon.type',
                    'battlePlayers.weapon.type.category',
                    'env',
                    'events',
                    'festTitle',
                    'festTitleAfter',
                    'freshnessModel',
                    'gender',
                    'hisTeamFestTheme',
                    'lobby',
                    'map',
                    'mode',
                    'myTeamFestTheme',
                    'rank',
                    'rank.group',
                    'rankAfter',
                    'rankAfter.group',
                    'rule',
                    'species',
                    'splatnetJson',
                    'user',
                    'user.env',
                    'user.userStat',
                    'user.userStat2',
                    'version',
                    'weapon',
                    'weapon.special',
                    'weapon.subweapon',
                    'weapon.type',
                    'weapon.type.category',
                    // }}}
                ]);

            foreach (['headgear', 'clothing', 'shoes'] as $_) {
                $query->with([
                    $_,
                    "{$_}.primaryAbility",
                    "{$_}.secondaries",
                    "{$_}.secondaries.ability",
                ]);
            }
        }

        if ($user) {
            $query->andWhere(['{{battle2}}.[[user_id]]' => $user->id]);
        }
        if ($model->newer_than != '') {
            $query->andWhere(['>', '{{battle2}}.[[id]]', (int)$model->newer_than]);
        }
        if ($model->older_than != '') {
            $query->andWhere(['<', '{{battle2}}.[[id]]', (int)$model->older_than]);
        }
        if ($model->order != '') {
            switch ($model->order) {
                case 'asc':
                case 'desc':
                default:
                    $query->orderBy(['id' => ($model->order == 'asc' ? SORT_ASC : SORT_DESC)]);
                    break;

                case 'splatnet_asc':
                case 'splatnet_desc':
                    $direction = $model->order === 'splatnet_asc' ? SORT_ASC : SORT_DESC;
                    $query
                        ->andWhere(['not', ['{{battle2}}.[[splatnet_number]]' => null]])
                        ->orderBy(['{{battle2}}.[[splatnet_number]]' => $direction]);
                    break;
            }
        }
        if ($model->count != '') {
            $query->limit((int)$model->count);
        }
        $res = Yii::$app->response;
        $res->format = $format === 'pretty' ? 'json' : 'compact-json';
        if ($model->only === 'splatnet_number') {
            $query->select(['splatnet_number'])->asArray();
            $result = [];
            foreach ($query->each(100) as $row) {
                $result[] = (int)$row['splatnet_number'];
            }
            return $result;
        } else {
            return array_map(
                fn ($model) => $model->toJsonArray(['events', 'splatnet_json']),
                $query->all(),
            );
        }
    }

    public function actionView($id, ?string $format = null)
    {
        if (!is_string($id) || !preg_match('/^\d+$/', $id)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $model = Battle2::find()
            ->withFreshness()
            ->andWhere(['battle2.id' => $id])
            ->limit(1)
            ->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $res = Yii::$app->response;
        $res->format = $format === 'pretty' ? 'json' : 'compact-json';
        return $model->toJsonArray();
    }

    public function actionPostableOptions(): Response
    {
        return $this->procOptionsRequest(true);
    }

    public function actionOptions(): Response
    {
        return $this->procOptionsRequest(false);
    }

    private function procOptionsRequest(bool $postable): Response
    {
        $res = Yii::$app->response;
        if (Yii::$app->request->method !== 'OPTIONS') {
            $res->statusCode = 405;
            return $res;
        }

        $allowMethods = $postable
            ? ['GET', 'HEAD', 'POST', 'OPTIONS']
            : ['GET', 'HEAD', 'OPTIONS'];

        $res->statusCode = 200;
        $header = $res->getHeaders();
        $header->set('Allow', implode(', ', $allowMethods));
        $header->set('Access-Control-Allow-Origin', '*');
        $header->set('Access-Control-Allow-Methods', implode(', ', $allowMethods));
        $header->set('Access-Control-Allow-Headers', 'Content-Type, Authenticate');
        $header->set('Access-Control-Max-Age', '86400');
        return $res;
    }
}
