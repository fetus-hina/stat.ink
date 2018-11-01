<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use app\models\Salmon2;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Cookie;

class SalmonController extends Controller
{
    public $layout = "main";

    public function behaviors()
    {
        return [
            [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => ['head', 'get'],
                ],
            ],
            // [
            //     'class' => AccessControl::class,
            //     'only' => [ 'edit-battle' ],
            //     'rules' => [
            //         [
            //             'actions' => [ 'edit-battle' ],
            //             'roles' => ['@'],
            //             'allow' => true,
            //         ],
            //     ],
            //     'ruleConfig' => [
            //         'class' => AccessRule::class,
            //         'matchCallback' => function ($rule, $action) {
            //             return $action->isEditable;
            //         },
            //     ],
            // ],
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

        $query = $user->getSalmonResults()
            ->with([
                'stage',
                'failReason',
                'titleBefore',
                'titleAfter',
            ]);

        return $this->render('index', [
            'user' => $user,
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
            ]),
            'spMode' => $this->getIndexViewMode() === 'simple',
        ]);
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
}
