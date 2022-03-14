<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\ostatus;

use Yii;
use app\components\helpers\BattleAtom;
use app\models\Battle;
use app\models\User;
use yii\web\Response;
use yii\web\ViewAction as BaseAction;

final class FeedAction extends BaseAction
{
    public $screen_name;

    public function run()
    {
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->language = 'ja-JP';

        if (!$user = $this->getUser()) {
            return $this->http404();
        }

        if (!$battleId = Yii::$app->getRequest()->get('battle')) {
            $resp = Yii::$app->getResponse();
            $resp->format = 'raw';
            $resp->getHeaders()->set('Content-Type', 'application/atom+xml; charset=UTF-8');
            $resp->data = BattleAtom::createUserFeed($user);
            return $resp;
        } elseif (!$battle = $this->getBattle($user, $battleId)) {
            return $this->http404();
        } else {
            $resp = Yii::$app->getResponse();
            $resp->format = 'raw';
            $resp->getHeaders()->set('Content-Type', 'application/atom+xml; charset=UTF-8');
            $resp->data = BattleAtom::createBattleFeed($user, $battle);
            return $resp;
        }
    }

    public function getUser(): ?User
    {
        $screenName = trim((string)Yii::$app->request->get('screen_name'));
        return User::find()
            ->andWhere([
                'screen_name' => $screenName,
            ])
            ->limit(1)
            ->one();
    }

    public function getBattle(User $user, int $battleId): ?Battle
    {
        return Battle::find()
            ->andWhere([
                'user_id' => $user->id,
                'id' => $battleId,
            ])
            ->limit(1)
            ->one();
    }

    public function http404(): Response
    {
        $resp = Yii::$app->response;
        $resp->format = 'json';
        $resp->statusCode = 404;
        $resp->statusText = 'Not Found';
        $resp->data = ['error' => Yii::t('yii', 'Page not found.')];
        $resp->content = null;
        return $resp;
    }
}
