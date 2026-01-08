<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Yii;
use app\models\Agent;
use app\models\Battle2;
use app\models\BattlePlayer2;
use app\models\Slack;
use app\models\User;
use app\models\UserStat2;
use yii\console\Controller;
use yii\db\Expression as DbExpr;
use yii\helpers\Console;

use function array_filter;
use function array_map;
use function escapeshellarg;
use function implode;
use function printf;
use function sprintf;
use function substr;
use function version_compare;

use const SORT_ASC;

class Battle2Controller extends Controller
{
    public function actionUserStat($id)
    {
        if (substr($id, 0, 1) === '@') {
            $user = User::findOne(['screen_name' => substr($id, 1)]);
        } else {
            $user = User::findOne(['id' => (int)$id]);
        }
        if (!$user) {
            $this->stderr("Could not find user {$id}\n");
            return 1;
        }

        if (!$model = UserStat2::findOne(['user_id' => $user->id])) {
            $model = Yii::createObject([
                'class' => UserStat2::class,
                'user_id' => $user->id,
            ]);
        }
        if (!$model->makeUpdate()->save()) {
            $this->stderr("Could not create/update stats\n");
            return 1;
        }
        $this->stderr("updated.\n");
        return 0;
    }

    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $battle = Battle2::findOne(['id' => (int)(string)$id]);
        if (!$battle) {
            $this->stderr("Could not find specified battle \"{$id}\"\n", Console::FG_RED);
            return 1;
        }
        $battle->delete();
        $transaction->commit();
        $this->stderr("updated.\n");
        return 0;
    }

    public function actionFixSquidtracks()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $this->stderr("Getting target agents...\n");
        $agentIds = array_filter(array_map(
            function (array $agent): ?int {
                if (
                    version_compare($agent['version'], '0.1.4', '>') &&
                    version_compare($agent['version'], '0.2.3', '<=')
                ) {
                    $this->stderr(sprintf("  version=%s, id=%d\n", $agent['version'], $agent['id']));
                    return $agent['id'];
                }
                return null;
            },
            Agent::find()->andWhere(['name' => 'SquidTracks'])->asArray()->all(),
        ));
        $this->stderr('done. id = [' . implode(', ', $agentIds) . "]\n");

        $query = Battle2::find()
            ->innerJoinWith('rule', false)
            ->with([
                'battlePlayers' => function ($query) {
                    $query->andWhere(['and',
                        ['not', ['is_my_team' => null]],
                        ['not', ['point' => null]],
                    ]);
                },
            ])
            ->andWhere(['and',
                ['battle2.agent_id' => $agentIds],
                ['not', ['battle2.is_win' => null]],
                ['<>', 'rule2.key', 'nawabari'],
            ])
            ->orderBy(['id' => SORT_ASC]);

        $count = $query->count();
        $this->stderr('Target battles = ' . $count . "\n");

        $i = -1;
        foreach ($query->batch(200) as $batch) {
            $myPointTargets = [];
            $playersTargets = [];
            foreach ($batch as $battle) {
                ++$i;

                printf("Working %d of %d, #%d\r", $i + 1, $count, $battle->id);

                if ($battle->is_win && $battle->my_point >= 1000) {
                    $myPointTargets[] = $battle->id;
                }

                foreach ($battle->battlePlayers as $player) {
                    if ($battle->is_win === $player->is_my_team && $player->point >= 1000) {
                        $playersTargets[] = $player->id;
                    }
                }
            }

            if ($myPointTargets) {
                echo "Updating battle2...\n";
                Battle2::updateAll(
                    ['my_point' => new DbExpr('my_point - 1000')],
                    ['id' => $myPointTargets],
                );
            }

            if ($playersTargets) {
                echo "Updating battle_player2...\n";
                BattlePlayer2::updateAll(
                    ['point' => new DbExpr('point - 1000')],
                    ['id' => $playersTargets],
                );
            }
        }

        echo "OK\n";

        $transaction->commit();
    }

    public function actionTestSlack($id)
    {
        $battle = Battle2::findOne(['id' => (int)(string)$id]);
        if (!$battle) {
            $this->stderr("Could not find specified battle \"{$id}\"\n", Console::FG_RED);
            return 1;
        }

        $list = Slack::find()
            ->andWhere([
                'user_id' => $battle->user->id,
                'suspended' => false,
            ])
            ->orderBy('id')
            ->all();
        foreach ($list as $slack) {
            printf(
                "curl -v -H %s -X POST -d %s %s\n\n",
                escapeshellarg('Content-Type: application/json'),
                escapeshellarg($slack->send($battle, false)),
                escapeshellarg($slack->webhook_url),
            );
        }
    }
}
