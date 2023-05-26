<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use LogicException;
use Yii;
use app\assets\BattleListGroupHeaderAsset;
use app\assets\SalmonEggAsset;
use app\components\helpers\TypeHelper;
use app\components\i18n\Formatter as FormatterEx;
use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Salmon3;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\UserStatBigrun3;
use app\models\UserStatEggstraWork3;
use yii\base\Widget;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\i18n\Formatter;
use yii\web\AssetManager;

use function array_filter;
use function array_map;
use function count;
use function implode;
use function vsprintf;

use const SORT_ASC;

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
            $schedule->is_eggstra_work === true => Yii::t('app-salmon3', 'Eggstra Work'),
            $schedule->big_map_id !== null => Yii::t('app-salmon3', 'Big Run'),
            default => throw new LogicException(),
        };
    }

    // private function renderWeapons(SalmonSchedule3 $schedule): ?string
    // {
    //     $models = SalmonScheduleWeapon3::find()
    //         ->with(['random', 'weapon'])
    //         ->andWhere(['schedule_id' => $schedule->id])
    //         ->orderBy(['id' => SORT_ASC])
    //         ->cache(3600)
    //         ->all();
    //     if (!$models) {
    //         return null;
    //     }

    //     return implode(
    //         '',
    //         array_map(
    //             fn (SalmonScheduleWeapon3 $weaponInfo): string => Html::tag(
    //                 'span',
    //                 WeaponIcon::widget([
    //                     'model' => $weaponInfo->weapon ?? $weaponInfo->random ?? null,
    //                 ]),
    //                 ['class' => 'mr-1'],
    //             ),
    //             $models,
    //         ),
    //     );
    // }

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

    // private function renderHighScore(Salmon3 $model, SalmonSchedule3 $schedule): ?string
    // {
    //     return match (true) {
    //         $schedule->big_map_id !== null => $this->renderBigRunHighScore($model, $schedule),
    //         $schedule->is_eggstra_work => $this->renderEggstraWorkHighScore($model, $schedule),
    //         default => null,
    //     };
    // }

    // private function renderBigRunHighScore(Salmon3 $model, SalmonSchedule3 $schedule): ?string
    // {
    //     $stats = UserStatBigrun3::find()
    //         ->andWhere([
    //             'schedule_id' => $schedule->id,
    //             'user_id' => $model->user_id,
    //         ])
    //         ->limit(1)
    //         ->one();
    //     $eggs = $stats?->golden_eggs ?? 0;
    //     return $eggs < 1 ? null : $this->renderHighScoreImpl($eggs);
    // }

    // private function renderEggstraWorkHighScore(Salmon3 $model, SalmonSchedule3 $schedule): ?string
    // {
    //     $stats = UserStatEggstraWork3::find()
    //         ->andWhere([
    //             'schedule_id' => $schedule->id,
    //             'user_id' => $model->user_id,
    //         ])
    //         ->limit(1)
    //         ->one();
    //     $eggs = $stats?->golden_eggs ?? 0;
    //     return $eggs < 1 ? null : $this->renderHighScoreImpl($eggs);
    // }

    // private function renderHighScoreImpl(int $eggs): string
    // {
    //     $am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);
    //     return Html::tag(
    //         'span',
    //         vsprintf('%s %s', [
    //             Html::img(
    //                 $am->getAssetUrl($am->getBundle(SalmonEggAsset::class), 'golden-egg.png'),
    //                 ['class' => 'basic-icon'],
    //             ),
    //             Html::encode(Yii::$app->formatter->asInteger($eggs)),
    //         ]),
    //         [
    //             'class' => 'auto-tooltip',
    //             'title' => Yii::t('app-salmon3', 'High Score'),
    //         ],
    //     );
    // }

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
