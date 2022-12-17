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
use app\models\BattleTricolorPlayer3;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

use const FILTER_VALIDATE_FLOAT;

final class BattleApiFormatter
{
    public static function toJson(
        ?Battle3 $model,
        bool $isAuthenticated = false,
        bool $fullTranslate = false,
    ): ?array {
        if (!$model) {
            return null;
        }

        $tricolor = $model->rule?->key === 'tricolor';
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
            'medals' => MedalApiFormatter::toJson($model->medals, $fullTranslate),
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
            'rank_exp_change' => $model->rank_exp_change,
            'rank_up_battle' => $model->is_rank_up_battle,
            'challenge_win' => $model->challenge_win,
            'challenge_lose' => $model->challenge_lose,
            'x_power_before' => self::formatPower($model->x_power_before),
            'x_power_after' => self::formatPower($model->x_power_after),
            'fest_power' => self::formatPower($model->fest_power),
            'fest_dragon' => DragonMatchApiFormatter::toJson($model->festDragon, $fullTranslate),
            'clout_before' => $model->clout_before,
            'clout_after' => $model->clout_after,
            'clout_change' => $model->clout_change,
            'cash_before' => $model->cash_before,
            'cash_after' => $model->cash_after,
            'our_team_color' => TeamColorApiFormatter::toJson($model->our_team_color, $fullTranslate),
            'their_team_color' => TeamColorApiFormatter::toJson($model->their_team_color, $fullTranslate),
            'third_team_color' => TeamColorApiFormatter::toJson($model->third_team_color, $fullTranslate),
            'our_team_role' => TricolorRoleApiFormatter::toJson($model->ourTeamRole, $fullTranslate),
            'their_team_role' => TricolorRoleApiFormatter::toJson($model->theirTeamRole, $fullTranslate),
            'third_team_role' => TricolorRoleApiFormatter::toJson($model->thirdTeamRole, $fullTranslate),
            'our_team_theme' => SplatfestThemeApiFormatter::toJson($model->ourTeamTheme, $fullTranslate),
            'their_team_theme' => SplatfestThemeApiFormatter::toJson($model->theirTeamTheme, $fullTranslate),
            'third_team_theme' => SplatfestThemeApiFormatter::toJson($model->thirdTeamTheme, $fullTranslate),
            'our_team_members' => BattlePlayerApiFormatter::toJson(
                self::filterPlayers(
                    $tricolor ? $model->battleTricolorPlayer3s : $model->battlePlayer3s,
                    true,
                    1,
                ),
                $fullTranslate,
            ),
            'their_team_members' => BattlePlayerApiFormatter::toJson(
                self::filterPlayers(
                    $tricolor ? $model->battleTricolorPlayer3s : $model->battlePlayer3s,
                    false,
                    2,
                ),
                $fullTranslate,
            ),
            'third_team_members' => BattlePlayerApiFormatter::toJson(
                self::filterPlayers(
                    $tricolor ? $model->battleTricolorPlayer3s : [],
                    false,
                    3,
                ),
                $fullTranslate,
            ),
            'note' => $model->note,
            'private_note' => $isAuthenticated ? $model->private_note : false,
            'link_url' => $model->link_url,
            'game_version' => SplatoonVersionApiFormatter::toJson($model->version, false),
            'user_agent' => UserAgentApiFormatter::toJson(
                $model->agent,
                $model->variables,
                $fullTranslate
            ),
            'automated' => $model->is_automated,
            'start_at' => DateTimeApiFormatter::toJson($model->start_at),
            'end_at' => DateTimeApiFormatter::toJson($model->end_at),
            'period' => PeriodApiFormatter::toJson($model->period),
            'created_at' => DateTimeApiFormatter::toJson($model->created_at),
        ];
    }

    /**
     * @param array<BattlePlayer3|BattleTricolorPlayer3> $players
     * @return Array<BattlePlayer3|BattleTricolorPlayer3>
     */
    private static function filterPlayers(array $players, bool $isOurTeam, int $team): array
    {
        return ArrayHelper::sort(
            \array_filter(
                $players,
                fn (BattlePlayer3|BattleTricolorPlayer3 $model): bool => $model instanceof BattlePlayer3
                    ? $model->is_our_team === $isOurTeam
                    : $model->team === $team,
            ),
            fn ($a, $b): int => $a->id <=> $b->id,
        );
    }

    private static function formatPower($value): ?JsExpression
    {
        $value = \filter_var($value, FILTER_VALIDATE_FLOAT);
        if (!\is_float($value)) {
            return null;
        }

        return new JsExpression(
            \sprintf('%.1f', $value),
        );
    }
}
