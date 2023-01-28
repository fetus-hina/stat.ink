<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3;

use Yii;
use app\models\Salmon3;
use app\models\Salmon3FilterForm;
use app\models\User;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_merge;
use function assert;
use function preg_match;
use function time;

use const SORT_DESC;

final class IndexAction extends Action
{
    /**
     * @return string|Response
     */
    public function run(string $screen_name)
    {
        if (!$user = User::findOne(['screen_name' => $screen_name])) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $controller = Yii::$app->controller;
        assert($controller instanceof Controller);

        // リスト表示モード切替
        $request = Yii::$app->request;
        if ((string)$request->get('v') !== '') {
            $view = $request->get('v');
            if ($view === 'simple' || $view === 'standard') {
                Yii::$app->response->cookies->add(
                    Yii::createObject([
                        'class' => Cookie::class,
                        'name' => 'work-list',
                        'value' => $view,
                        'expire' => time() + 86400 * 366,
                    ]),
                );
            }

            $next = $_GET;
            unset($next['v']);
            $next[0] = 'salmon-v3/index';
            return $controller->redirect(Url::to($next));
        }

        $query = Salmon3::find()
            ->with([
                'bigStage',
                'failReason',
                'kingSalmonid',
                'salmonPlayer3s' => function (ActiveQuery $query): void {
                    $query->onCondition(['{{%salmon_player3}}.[[is_me]]' => true]);
                },
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon',
                'salmonPlayer3s.special',
                'schedule',
                'stage',
                'titleAfter',
                'titleBefore',
            ])
            ->andWhere([
                'is_deleted' => false,
                'user_id' => $user->id,
            ])
            ->orderBy([
                'start_at' => SORT_DESC,
                'id' => SORT_DESC,
            ]);

        $form = Yii::createObject(Salmon3FilterForm::class);
        if ($form->load($_GET) && $form->validate()) {
            $form->decorateQuery($query);
        }

        return $controller->render('index', [
            'dataProvider' => Yii::createObject([
                'class' => ActiveDataProvider::class,
                'query' => $query,
                'sort' => false,
            ]),
            'filter' => $form,
            'permLink' => Url::to(
                array_merge(
                    $form->toPermLink(),
                    ['salmon-v3/index', 'screen_name' => $user->screen_name],
                ),
                true,
            ),
            'spMode' => $this->getIndexViewMode() === 'simple',
            'user' => $user,
        ]);
    }

    private function getIndexViewMode(): string
    {
        $request = Yii::$app->request;
        $mode = null;
        if ($cookie = $request->cookies->get('work-list')) {
            $mode = $cookie->value;
        }

        if ($mode === 'simple' || $mode === 'standard') {
            return $mode;
        }

        $ua = (string)$request->userAgent;
        return preg_match('/iP[ao]d|iPhone|Android/', $ua)
            ? 'simple'
            : 'standard';
    }
}
