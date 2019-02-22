<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\i18n\Formatter;
use app\components\web\Controller;
use app\models\Language;
use app\models\Salmon2;
use app\models\Salmon2DeleteForm;
use app\models\Salmon2FilterForm;
use app\models\User;
use jp3cki\uuid\NS as UuidNS;
use jp3cki\uuid\Uuid;
use jp3cki\yii2\feed\Feed;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\Response;

class SalmonController extends Controller
{
    public $layout = "main";

    public function behaviors()
    {
        return [
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'edit' => ['head', 'get', 'post'],
                    '*' => ['head', 'get'],
                ],
            ],
            [
                'class' => AccessControl::class,
                'only' => [
                    'delete',
                    'edit',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'delete',
                            'edit',
                        ],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
                'ruleConfig' => [
                    'class' => AccessRule::class,
                    'matchCallback' => function ($rule, $action): bool {
                        $model = Salmon2::findOne([
                          'id' => Yii::$app->getRequest()->get('id'),
                        ]);
                        if (!$model) {
                            static::error404();
                            return false;
                        }
                        return $model->isEditable;
                    },
                ],
            ],
        ];
    }

    public function actionIndex(string $screen_name): ?string
    {
        $user = User::findOne(['screen_name' => $screen_name]);
        if (!$user) {
            static::error404();
            return null;
        }

        // リスト表示モード切替
        $request = Yii::$app->getRequest();
        if ($request->get('v') != '') {
            $view = $request->get('v');
            if ($view === 'simple' || $view === 'standard') {
                Yii::$app->response->cookies->add(
                    new Cookie([
                        'name' => 'work-list',
                        'value' => $view,
                        'expire' => time() + 86400 * 366,
                    ])
                );
            }

            $next = $_GET;
            unset($next['v']);
            $next[0] = 'salmon/index';
            $this->redirect(Url::to($next));
            return null;
        }

        $filter = Yii::createObject(Salmon2FilterForm::class);
        $filter->load($_GET);

        $query = Salmon2::find()
            ->orderBy(['id' => SORT_DESC])
            ->andWhere(['user_id' => $user->id])
            ->with([
                'failReason',
                'players',
                'players.gender',
                'players.special',
                'players.species',
                'stage',
                'titleAfter',
                'titleBefore',
                'waves',
            ]);
        $filter->decorateQuery($query);

        return $this->render('index', [
            'user' => $user,
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
            ]),
            'spMode' => $this->getIndexViewMode() === 'simple',
            'filter' => $filter,
        ]);
    }

    public function actionFeed(string $screen_name, string $lang, string $type): ?Response
    {
        $user = User::findOne(['screen_name' => $screen_name]);
        if (!$user) {
            static::error404();
            return null;
        }

        $langModel = Language::findOne(['lang' => $lang]);
        if (!$langModel) {
            static::error404();
            return null;
        }
        Yii::$app->language = $langModel->lang;
        Yii::$app->timeZone = 'Etc/UTC';

        $fmt = Yii::createObject([
            'class' => Formatter::class,
            'nullDisplay' => '?',
        ]);

        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_RAW;
        $resp->headers->set('content-type', sprintf(
            '%s; charset=UTF-8',
            ($type === 'atom') ? 'application/atom+xml' : 'application/rss+xml'
        ));
        $resp->data = Feed::widget([
            'type' => ($type === 'atom') ? Feed::TYPE_ATOM : Feed::TYPE_RSS,
            'dataProvider' => new ActiveDataProvider([
                'query' => Salmon2::find()
                    ->orderBy(['id' => SORT_DESC])
                    ->andWhere(['user_id' => $user->id])
                    ->with([
                        'failReason',
                        'players',
                        'players.special',
                        'stage',
                        'titleAfter',
                        'titleBefore',
                        'user',
                    ]),
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]),
            'title' => Yii::t('app-salmon2', '{name}\'s Salmon Log', ['name' => $user->name]),
            'description' => Yii::t('app-salmon2', '{name}\'s Salmon Log', ['name' => $user->name]),
            'copyright' => sprintf(
                'Copyright (C) 2015-%d AIZAWA Hina',
                gmdate('Y', $_SERVER['REQUEST_TIME'] ?? time())
            ),
            'link' => Url::home(true),
            'rssLink' => Url::to(['salmon/feed',
                'screen_name' => $user->screen_name,
                'lang' => $langModel->lang,
                'type' => 'rss',
            ], true),
            'atomLink' => Url::to(['salmon/feed',
                'screen_name' => $user->screen_name,
                'lang' => $langModel->lang,
                'type' => 'atom',
            ], true),
            'author' => [
                'name' => 'stat.ink',
                'uri' => Url::home(true),
            ],
            'generator' => [
                'name' => Yii::$app->name,
                'version' => Yii::$app->version,
                'uri' => Url::home(true),
            ],
            'dateCreated' => null, // null means "now"
            'dateModified' => null,
            'lastBuildDate' => null,
            'entry' => [
                'id' => function (Salmon2 $model): string {
                    return Uuid::v5(
                        UuidNS::url(),
                        Url::to(['salmon/view',
                            'screen_name' => $model->user->screen_name,
                            'id' => $model->id,
                        ], true)
                    )->formatAsUri();
                },
                'title' => function (Salmon2 $model): string {
                    $result = '?';
                    if ($model->clear_waves !== null) {
                        if ($model->clear_waves == 3) {
                            $result = Yii::t('app-salmon2', 'Cleared');
                        } else {
                            $result = Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                                'waveNumber' => $model->clear_waves + 1,
                            ]);
                            if ($model->failReason) {
                                $result .= ' / ' . Yii::t('app-salmon2', $model->failReason->name);
                            }
                        }
                    }

                    return vsprintf('%s - %s (#%d)', [
                        implode(' / ', [
                            Yii::t('app-salmon2', 'Salmon Run'),
                            Yii::t('app-salmon-map2', $model->stage->name ?? '?'),
                            $result,
                        ]),
                        $model->user->name,
                        $model->id,
                    ]);
                },
                'link' => function (Salmon2 $model): string {
                    return Url::to(['salmon/view',
                        'screen_name' => $model->user->screen_name,
                        'id' => $model->id,
                    ], true);
                },
                'dateModified' => function (Salmon2 $model): DateTimeImmutable {
                    return (new DateTimeImmutable($model->updated_at))
                        ->setTimeZone(new DateTimeZone('Etc/UTC'));
                },
                'dateCreated' => function (Salmon2 $model): DateTimeImmutable {
                    return (new DateTimeImmutable($model->created_at))
                        ->setTimeZone(new DateTimeZone('Etc/UTC'));
                },
                'content' => function (Salmon2 $model) use ($fmt): string {
                    $data = [
                        [
                            Yii::t('app', 'Stage'),
                            Yii::t('app-salmon-map2', $model->stage->name ?? '?'),
                        ],
                        [
                            Yii::t('app-salmon2', 'Hazard Level'),
                            $model->danger_rate === null
                                ? '?'
                                : vsprintf('%s (%s: %s)', [
                                    $fmt->asDecimal($model->danger_rate, 1),
                                    Yii::t('app-salmon2', 'Quota'),
                                    implode(' - ', $model->quota ?? []),
                                ]),
                        ],
                        [
                            Yii::t('app', 'Special'),
                            Yii::t('app-special2', $model->myData->special->name ?? null),
                        ],
                        [
                            Yii::t('app-salmon2', 'Deaths'),
                            $fmt->asInteger($model->myData->death ?? null),
                        ],
                        [
                            Yii::t('app-salmon2', 'Rescues'),
                            $fmt->asInteger($model->myData->rescue ?? null),
                        ],
                    ];

                    return Html::tag('dl', implode('', array_map(function (array $row): string {
                        return Html::tag('dt', Html::encode($row[0])) . Html::tag('dd', Html::encode($row[1]));
                    }, $data)));
                },
            ],
        ]);
        return $resp;
    }

    public function actionView(string $screen_name, int $id): ?string
    {
        $model = Salmon2::findOne(['id' => $id]);
        if (!$model || !$model->user) {
            static::error404();
            return null;
        }

        if ($model->user->screen_name !== $screen_name) {
            $this->redirect(
                ['salmon/view', 'id' => $model->id, 'screen_name' => $model->user->screen_name],
                301
            );
            return null;
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function getIndexViewMode(): string
    {
        $request = Yii::$app->getRequest();
        $mode = null;
        if ($cookie = $request->cookies->get('work-list')) {
            $mode = $cookie->value;
        }

        if ($mode === 'simple' || $mode === 'standard') {
            return $mode;
        }

        $ua = $request->userAgent;
        if (strpos($ua, 'iPod') !== false || strpos($ua, 'iPhone') !== false) {
            return 'simple';
        }

        if (strpos($ua, 'Android') !== false) {
            return 'simple';
        }

        if (strpos($ua, 'Windows Phone') !== false) {
            return 'simple';
        }

        return 'standard';
    }

    public function actionEdit(string $screen_name, int $id)
    {
        $model = Salmon2::findOne([
            'id' => $id,
        ]);
        if (!$model || !$model->user) {
            static::error404();
            return null;
        }
        if ($model->user->screen_name !== $screen_name) {
            $this->redirect(
                ['salmon/view',
                    'id' => $model->id,
                    'screen_name' => $model->user->screen_name,
                ]
            );
            return null;
        }
        if (!$model->isEditable) {
            static::error403();
            return null;
        }

        return $this->render('edit', [
            'model' => $model,
            'deleteForm' => Yii::createObject(Salmon2DeleteForm::class),
        ]);
    }

    public function actionDelete(string $screen_name, int $id)
    {
        $model = Salmon2::findOne([
            'id' => $id,
        ]);
        if (!$model || !$model->user) {
            static::error404();
            return null;
        }
        if ($model->user->screen_name !== $screen_name) {
            $this->redirect(
                ['salmon/view',
                  'id' => $model->id,
                  'screen_name' => $model->user->screen_name,
                ]
            );
            return null;
        }
        if (!$model->isEditable) {
            static::error403();
            return null;
        }

        $form = Yii::createObject(Salmon2DeleteForm::class);
        $form->load(Yii::$app->getRequest()->post());
        $form->model = $model;
        if ($form->delete()) {
            $this->redirect(
                ['salmon/index',
                    'screen_name' => $model->user->screen_name,
                ]
            );
            return null;
        }

        return $this->render('edit', [
            'model' => $model,
            'deleteForm' => $form,
            //TODO: editForm
        ]);
    }
}
