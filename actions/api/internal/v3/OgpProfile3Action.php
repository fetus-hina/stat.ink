<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\internal\v3;

use Yii;
use app\components\formatters\api\v3\UserApiFormatter;
use app\components\helpers\TypeHelper;
use app\models\BattlePlayer3;
use app\models\Rank3;
use app\models\User;
use app\models\UserStat3XMatch;
use app\models\UserStatSalmon3;
use yii\base\Action;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function is_string;
use function preg_match;
use function sprintf;

use const SORT_DESC;

final class OgpProfile3Action extends Action
{
    public User|null $user = null;

    public function init()
    {
        parent::init();

        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $req = Yii::$app->request;
        $screenName = $req->get('screen_name');
        if (
            !is_string($screenName) ||
            !preg_match('/^[0-9A-Za-z_]+$/', $screenName)
        ) {
            throw new BadRequestHttpException();
        }

        $this->user = User::find()
            ->andWhere(['screen_name' => $screenName])
            ->limit(1)
            ->one();
        if (!$this->user) {
            throw new NotFoundHttpException();
        }
    }

    public function run()
    {
        $user = TypeHelper::instanceOf($this->user, User::class);

        return Yii::$app->db->transaction(
            fn () => [
                'user' => UserApiFormatter::toJson($user, false, false),
                'player' => $this->playerData($user),
                'battles' => $this->battleData($user),
                'salmon' => $this->salmonData($user),
                'x_peak' => $this->xPeakData($user),
            ],
            Transaction::READ_COMMITTED,
        );
    }

    private function playerData(User $user): ?array
    {
        $player = BattlePlayer3::find()
            ->innerJoinWith(['battle'], false)
            ->with(['splashtagTitle'])
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                    '{{%battle_player3}}.[[is_me]]' => true,
                ],
                ['not', ['{{%battle_player3}}.[[name]]' => null]],
                ['not', ['{{%battle_player3}}.[[number]]' => null]],
                ['<>', '{{%battle_player3}}.[[name]]', ''],
                ['<>', '{{%battle_player3}}.[[number]]', ''],
            ])
            ->orderBy([
                '{{%battle3}}.[[end_at]]' => SORT_DESC,
                '{{%battle3}}.[[id]]' => SORT_DESC,
            ])
            ->limit(1)
            ->cache(300)
            ->one();
        return $player
            ? [
                'name' => $player->name,
                'number' => $player->number,
                'title' => $player->splashtagTitle?->name,
            ]
            : null;
    }

    private function battleData(User $user): ?array
    {
        $data = (new Query())
            ->select([
                'battles' => 'SUM([[battles]])',
                'wins' => 'SUM([[wins]])',
                'kills' => 'SUM([[kills]])',
                'deaths' => 'SUM([[deaths]])',
                'seconds' => 'SUM([[agg_seconds]])',
                'peak_rank_id' => 'MAX([[peak_rank_id]])',
                'peak_s_plus' => 'MAX([[peak_s_plus]])',
            ])
            ->from('{{%user_stat3}}')
            ->andWhere(['user_id' => $user->id])
            ->cache(300)
            ->one();
        if (!$data) {
            return null;
        }

        $peakRank = null;
        if ($data['peak_rank_id']) {
            $rank = Rank3::find()
                ->andWhere(['id' => $data['peak_rank_id']])
                ->limit(1)
                ->cache(86400)
                ->one();
            if ($rank) {
                if ($rank->key === 's+' && $data['peak_s_plus'] !== null) {
                    $peakRank = sprintf('%s %d', $rank->name, $data['peak_s_plus']);
                } else {
                    $peakRank = $rank->name;
                }
            }
        }

        return [
            'battles' => (int)$data['battles'],
            'wins' => (int)$data['wins'],
            'kills' => $data['seconds'] > 0 ? $data['kills'] / ($data['seconds'] / 60.0) : null,
            'deaths' => $data['seconds'] > 0 ? $data['deaths'] / ($data['seconds'] / 60.0) : null,
            'peak_rank' => $peakRank,
        ];
    }

    private function salmonData(User $user): ?array
    {
        $model = UserStatSalmon3::find()
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->cache(300)
            ->one();
        if (!$model) {
            return null;
        }

        $bigRun = (new Query())
            ->select(['golden_eggs' => 'MAX([[golden_eggs]])'])
            ->from('{{%user_stat_bigrun3}}')
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->cache(300)
            ->one();

        $eggstra = (new Query())
            ->select(['golden_eggs' => 'MAX([[golden_eggs]])'])
            ->from('{{%user_stat_eggstra_work3}}')
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->cache(300)
            ->one();

        return [
            'jobs' => (int)$model->jobs,
            'cleared' => (int)$model->clear_jobs,
            'waves' => $model->agg_jobs > 0 ? $model->clear_waves / $model->agg_jobs : null,
            'king' => (int)$model->king_defeated,
            'big_run' => TypeHelper::intOrNull($bigRun['golden_eggs'] ?? null),
            'eggstra_work' => TypeHelper::intOrNull($eggstra['golden_eggs'] ?? null),
        ];
    }

    private function xPeakData(User $user): ?array
    {
        $models = UserStat3XMatch::find()
            ->with(['rule'])
            ->andWhere(['user_id' => $user->id])
            ->cache(300)
            ->all();
        $data = ArrayHelper::map($models, 'rule.key', 'peak_x_power');
        if (!$data) {
            return null;
        }

        return [
            'area' => TypeHelper::floatOrNull(ArrayHelper::getValue($data, 'area')),
            'yagura' => TypeHelper::floatOrNull(ArrayHelper::getValue($data, 'yagura')),
            'hoko' => TypeHelper::floatOrNull(ArrayHelper::getValue($data, 'hoko')),
            'asari' => TypeHelper::floatOrNull(ArrayHelper::getValue($data, 'asari')),
        ];
    }
}
