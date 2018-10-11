<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\VerbFilter;

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
            ]),
        ]);
    }
}
