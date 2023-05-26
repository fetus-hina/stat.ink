<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3\s3s;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\models\Battle3;
use app\models\Lobby3;
use app\models\User;
use yii\base\Action;
use yii\db\Transaction;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

use function array_merge;

use const SORT_DESC;

final class UuidListAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit('compact-json');
    }

    public function run(?string $lobby = null): Response
    {
        $user = Yii::$app->user->identity;
        if (!$user instanceof IdentityInterface) {
            throw new UnauthorizedHttpException();
        }

        $resp = Yii::$app->response;
        $resp->statusCode = 200;
        $resp->content = null;
        $resp->data = Yii::$app->db->transaction(
            fn () => self::getList($user, $lobby),
            Transaction::REPEATABLE_READ,
        );

        return $resp;
    }

    /**
     * @param int<1, max> $limit
     * @return string[]
     */
    private static function getList(User $user, ?string $lobby, int $limit = 500): array
    {
        if ($lobby === 'adaptive') {
            $lobbies = [
                'regular',
                'bankara_challenge',
                'bankara_open',
                'splatfest_challenge',
                'splatfest_open',
                // TODO: event
                'xmatch',
                'private',
            ];

            $results = [];
            foreach ($lobbies as $lobby) {
                $results = array_merge($results, self::getList($user, $lobby, 50));
            }

            return $results;
        }

        $query = Battle3::find()
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->andWhere(['not', ['client_uuid' => null]])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit);

        if ($lobby) {
            $query->andWhere(['lobby_id' => self::getLobbyIdByKey($lobby)]);
        }

        $results = [];
        foreach ($query->each() as $model) {
            $results[] = $model->client_uuid;
        }

        return $results;
    }

    /**
     * @return int|int[]
     */
    private static function getLobbyIdByKey(string $lobby)
    {
        if ($lobby === 'bankara') {
            return [
                self::getLobbyIdByKey('bankara_challenge'),
                self::getLobbyIdByKey('bankara_open'),
            ];
        }

        $model = Lobby3::find()
            ->andWhere(['key' => $lobby])
            ->limit(1)
            ->one();
        if (!$model) {
            throw new BadRequestHttpException("Unknown lobby key {$lobby}");
        }

        return (int)$model->id;
    }
}
