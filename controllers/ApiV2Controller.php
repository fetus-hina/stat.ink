<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;

class ApiV2Controller extends Controller
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
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'salmon' => ['head', 'get', 'post'],
                    'salmon-stats' => ['head', 'get', 'post'],
                    '*' => ['head', 'get'],
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'only' => [
                    'salmon',
                    'salmon-stats',
                ],
            ],
        ];
    }

    public function actions()
    {
        $prefix = 'app\actions\api\v2';
        return [
            'gear' => [
                'class' => $prefix . '\GearAction',
            ],
            'rule' => [
                'class' => $prefix . '\RuleAction',
            ],
            'post salmon' => [
                'class' => $prefix . '\salmon\PostSalmonAction',
            ],
            'salmon-stats' => [
                'class' => $prefix . '\salmon\SalmonStatsAction',
            ],
            'stage' => [
                'class' => $prefix . '\StageAction',
            ],
            'weapon' => [ 
                'class' => $prefix . '\WeaponAction',
            ],
            // 'death-reason'  => [ 'class' => $prefix . '\DeathReasonAction' ],
            // 'user'          => [ 'class' => $prefix . '\UserAction' ],
            // 'weapon-trends' => [ 'class' => $prefix . '\WeaponTrendsAction' ],
        ];
    }

    public function actionMap()
    {
        return $this->redirect(
            array_merge(Yii::$app->request->get(), ['/api-v2/stage']),
            301
        );
    }

    public function createAction($id)
    {
        if ($id === '') {
            $id = $this->defaultAction;
        }

        $method = strtolower(Yii::$app->getRequest()->getMethod());

        $actionMap = $this->actions();
        if (isset($actionMap["{$method} {$id}"])) {
            return Yii::createObject(
                $actionMap["{$method} {$id}"],
                [$id, $this]
            );
        } elseif (isset($actionMap[$id])) {
            return Yii::createObject($actionMap[$id], [$id, $this]);
        } elseif (preg_match('/^[a-z0-9\\-_]+$/', $id) &&
            strpos($id, '--') === false &&
            trim($id, '-') === $id
        ) {
            $methodName = 'action' . str_replace(' ', '', ucwords(str_replace('-', ' ', $id)));
            if (method_exists($this, $methodName)) {
                $method = new \ReflectionMethod($this, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return new InlineAction($id, $this, $methodName);
                }
            }
        }
        return null;
    }
}
