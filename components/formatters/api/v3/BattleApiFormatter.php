<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Battle3;
use app\models\BattlePlayer3;
use yii\helpers\Url;

final class BattleApiFormatter
{
    public static function toJson(
        ?Battle3 $model,
        bool $isAuthenticated = false,
        bool $fullTranslate = false
    ): ?array {
        if (!$model) {
            return null;
        }

        return [
            'id' => $model->uuid,
            'url' => Url::to(
                ['/show-v3/battle',
                    'screen_name' => $model->user->screen_name,
                    'battle' => $model->uuid,
                ],
                true
            ),
            'images' => [
                'judge' => ImageApiFormatter::toJson($model->battleImageJudge3),
                'results' => ImageApiFormatter::toJson($model->battleImageResult3),
                'gear' => ImageApiFormatter::toJson($model->battleImageGear3),
            ],
            'user' => UserApiFormatter::toJson($model->user, $isAuthenticated, $fullTranslate),
            'lobby' => LobbyApiFormatter::toJson($model->lobby, $fullTranslate),
            'rule' => RuleApiFormatter::toJson($model->rule, $fullTranslate),
            'stage' => StageApiFormatter::toJson($model->map, $fullTranslate),
            'weapon' => WeaponApiFormatter::toJson($model->weapon, $fullTranslate),
            'result' => ResultApiFormatter::toJson($model->result),
            'knockout' => $model->is_knockout,
            'rank_in_team' => $model->rank_in_team,
            'kill' => $model->kill,
            'assist' => $model->assist,
            'kill_or_assist' => $model->kill_or_assist,
            'death' => $model->death,
            'special' => $model->special,
            'inked' => $model->inked,
            'our_team_inked' => $model->our_team_inked,
            'their_team_inked' => $model->their_team_inked,
            'our_team_percent' => $model->our_team_percent,
            'their_team_percent' => $model->their_team_percent,
            'our_team_count' => $model->our_team_count,
            'their_team_count' => $model->their_team_count,
            'level_before' => $model->level_before,
            'level_after' => $model->level_after,
            'rank_before' => RankApiFormatter::toJson($model->rankBefore, $fullTranslate),
            'rank_before_s_plus' => $model->rank_before_s_plus,
            'rank_before_exp' => $model->rank_before_exp,
            'rank_after' => RankApiFormatter::toJson($model->rankAfter, $fullTranslate),
            'rank_after_s_plus' => $model->rank_after_s_plus,
            'rank_after_exp' => $model->rank_after_exp,
            'cash_before' => $model->cash_before,
            'cash_after' => $model->cash_after,
            'our_team_members' => BattlePlayerApiFormatter::toJson(
                self::filterPlayers($model->battlePlayer3s, true),
                $fullTranslate,
            ),
            'their_team_members' => BattlePlayerApiFormatter::toJson(
                self::filterPlayers($model->battlePlayer3s, false),
                $fullTranslate,
            ),
            'note' => $model->note,
            'private_note' => $isAuthenticated ? $model->private_note : false,
            'link_url' => $model->link_url,
            'game_version' => SplatoonVersionApiFormatter::toJson($model->version, false),
            'user_agent' => UserAgentApiFormatter::toJson($model->agent, $fullTranslate),
            'automated' => $model->is_automated,
            'start_at' => DateTimeApiFormatter::toJson($model->start_at),
            'end_at' => DateTimeApiFormatter::toJson($model->end_at),
            'period' => PeriodApiFormatter::toJson($model->period),
            'created_at' => DateTimeApiFormatter::toJson($model->created_at),
        ];
    }

    /**
     * @param BattlePlayer3[] $players
     * @return BattlePlayer3[]
     */
    private static function filterPlayers(array $players, bool $isOurTeam): array
    {
        $players = \array_filter(
            $players,
            function (BattlePlayer3 $model): bool {
                return $model->is_our_team;
            }
        );
        usort($players, fn (BattlePlayer3 $a, BattlePlayer3 $b): int => $a->id <=> $b->id);
        return \array_values($players);
    }
}
