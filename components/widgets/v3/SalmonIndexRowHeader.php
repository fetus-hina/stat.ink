<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use LogicException;
use Yii;
use app\assets\BattleListGroupHeaderAsset;
use app\components\helpers\TypeHelper;
use app\components\i18n\Formatter as FormatterEx;
use app\components\widgets\Icon;
use app\models\Salmon3;
use app\models\SalmonSchedule3;
use yii\base\Widget;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\i18n\Formatter;

use function array_filter;
use function count;
use function implode;

final class SalmonIndexRowHeader extends Widget
{
    public Salmon3|null $model = null;
    public GridView|null $gridView = null;

    private static int|false|null $lastScheduleId = false;
    private static bool|null $lastIsPrivate = null;

    public function run(): ?string
    {
        $widget = TypeHelper::instanceOf($this->gridView, GridView::class);
        $model = TypeHelper::instanceOf($this->model, Salmon3::class);
        if (
            self::$lastScheduleId === $model->schedule_id &&
            self::$lastIsPrivate === $model->is_private
        ) {
            return null;
        }

        self::$lastScheduleId = $model->schedule_id;
        self::$lastIsPrivate = $model->is_private;

        return match (true) {
            $model->is_private => $this->renderPrivate($widget),
            $model->schedule_id === null => $this->renderUnknown($widget),
            default => $this->renderSchedule($model, $model->schedule, $widget),
        };
    }

    private function renderPrivate(GridView $widget): string
    {
        return $this->decorateRow(
            Html::encode(Yii::t('app-salmon3', 'Private Job')),
            $widget,
        );
    }

    private function renderUnknown(GridView $widget): string
    {
        return $this->decorateRow(
            Html::encode(Yii::t('app', 'Unknown')),
            $widget,
        );
    }

    private function renderSchedule(Salmon3 $model, SalmonSchedule3 $schedule, GridView $widget): string
    {
        return $this->decorateRow(
            implode(
                ' ',
                array_filter(
                    [
                        $this->renderMode($schedule),
                        // $this->renderWeapons($schedule),
                        $this->renderTerm($schedule, $widget->formatter),
                        // $this->renderHighScore($model, $schedule),
                        $this->renderStats($schedule),
                    ],
                    fn (?string $html): bool => $html !== null,
                ),
            ),
            $widget,
        );
    }

    private function renderMode(SalmonSchedule3 $schedule): ?string
    {
        if (
            !$schedule->is_eggstra_work &&
            !$schedule->big_map_id
        ) {
            return null;
        }

        return match (true) {
            $schedule->is_eggstra_work === true => Icon::s3Eggstra(),
            $schedule->big_map_id !== null => Icon::s3BigRun(),
            default => throw new LogicException(),
        };
    }

    private function renderTerm(SalmonSchedule3 $schedule, Formatter $formatter): string
    {
        $f = match ($formatter::class) {
            FormatterEx::class => fn (mixed $v): string => $formatter->asHtmlDatetimeEx($v, 'medium', 'short'),
            default => fn (mixed $v): string => $formatter->asDatetime($v, 'medium', 'short'),
        };

        return Yii::t('app', '{from} - {to}', [
            'from' => $f($schedule->start_at),
            'to' => $f($schedule->end_at),
        ]);
    }

    private function renderStats(SalmonSchedule3 $schedule): string
    {
        $user = $this->model?->user;
        if (!$user) {
            return '';
        }

        return Html::a(
            Icon::stats(),
            ['salmon-v3/stats-schedule',
                'screen_name' => $user->screen_name,
                'schedule' => $schedule->id,
            ],
        );
    }

    private function decorateRow(string $html, GridView $widget): string
    {
        BattleListGroupHeaderAsset::register($this->view);

        return Html::tag(
            'tr',
            Html::tag(
                'td',
                $html,
                [
                    'class' => 'battle-row-group-header',
                    'colspan' => (string)count($widget->columns),
                ],
            ),
        );
    }
}
