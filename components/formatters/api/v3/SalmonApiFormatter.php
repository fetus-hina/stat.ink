<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use TypeError;
use app\components\formatters\api\v3\traits\FloatValTrait;
use app\components\helpers\TypeHelper;
use app\models\Salmon3;
use app\models\SalmonKing3;
use yii\helpers\Url;

final class SalmonApiFormatter
{
    use FloatValTrait;

    public static function toJson(
        ?Salmon3 $model,
        bool $isAuthenticated = false,
        bool $fullTranslate = false,
    ): ?array {
        if (!$model) {
            return null;
        }

        return [
            'id' => $model->uuid,
            'url' => Url::to(
                ['/salmon-v3/view',
                    'screen_name' => $model->user->screen_name,
                    'battle' => $model->uuid,
                ],
                true,
            ),
            'user' => UserApiFormatter::toJson($model->user, $isAuthenticated, $fullTranslate),
            'uuid' => $model->client_uuid,
            'private' => $model->is_private,
            'big_run' => $model->is_big_run,
            'eggstra_work' => $model->is_eggstra_work,
            'stage' => SalmonStageApiFormatter::toJson(
                $model->stage,
                $model->bigStage,
                $fullTranslate,
            ),
            'danger_rate' => self::floatVal($model->danger_rate),
            'clear_waves' => $model->clear_waves,
            'fail_reason' => SalmonFailReasonApiFormatter::toJson($model->failReason, $fullTranslate),
            'king_smell' => $model->king_smell,
            'king_salmonid' => SalmonKingApiFormatter::toJson($model->kingSalmonid, $fullTranslate),
            'guessed_king_salmonid' => SalmonKingApiFormatter::toJson(
                self::guessKingSalmonid($model),
                $fullTranslate,
            ),
            'clear_extra' => $model->clear_extra,
            'title_before' => SalmonTitleApiFormatter::toJson($model->titleBefore, $fullTranslate),
            'title_exp_before' => $model->title_exp_before,
            'title_after' => SalmonTitleApiFormatter::toJson($model->titleAfter, $fullTranslate),
            'title_exp_after' => $model->title_exp_after,
            'golden_eggs' => $model->golden_eggs,
            'power_eggs' => $model->power_eggs,
            'gold_scale' => $model->gold_scale,
            'silver_scale' => $model->silver_scale,
            'bronze_scale' => $model->bronze_scale,
            'job_point' => $model->job_point,
            'job_score' => $model->job_score,
            'job_rate' => self::floatVal($model->job_rate),
            'job_bonus' => $model->job_bonus,
            'players' => SalmonPlayerApiFormatter::allToJson($model->salmonPlayer3s, $fullTranslate),
            'waves' => SalmonWaveApiFormatter::allToJson($model->salmonWave3s, $fullTranslate),
            'bosses' => SalmonBossAppearanceApiFormatter::allToJson(
                $model->salmonBossAppearance3s,
                $fullTranslate,
            ),
            'note' => $model->note,
            'private_note' => $isAuthenticated ? $model->private_note : false,
            'link_url' => $model->link_url,
            'game_version' => SplatoonVersionApiFormatter::toJson($model->version, false),
            'user_agent' => UserAgentApiFormatter::toJson(
                $model->agent,
                $model->variables,
                $fullTranslate,
            ),
            'automated' => $model->is_automated,
            'start_at' => DateTimeApiFormatter::toJson($model->start_at),
            'end_at' => DateTimeApiFormatter::toJson($model->end_at),
            'period' => PeriodApiFormatter::toJson($model->period),
            'shift' => SalmonScheduleApiFormatter::toJson($model->schedule),
            'created_at' => DateTimeApiFormatter::toJson($model->created_at),
        ];
    }

    private static function guessKingSalmonid(Salmon3 $model): ?SalmonKing3
    {
        try {
            if (version_compare($model->version?->tag ?? '0.0.0', '6.0.0', '<')) {
                return null;
            }

            $schedule = $model->schedule;
            if (!$schedule) {
                return null;
            }

            $startAt = TypeHelper::int(strtotime($schedule->start_at));
            if ($startAt < TypeHelper::int(strtotime('2023-12-01T00:00:00+00:00'))) {
                return null;
            }

            return $schedule->king;
        } catch (TypeError) {
            return null;
        }
    }
}
