<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\indexSchedule;

use Yii;
use app\assets\Spl2WeaponAsset;
use app\models\SalmonMap2;
use app\models\SalmonSchedule2;
use app\models\SalmonWeapon2;
use statink\yii2\stages\spl2\Spl2Stage;
use stdClass;
use yii\base\Widget;
use yii\helpers\Html;

class SalmonShifts extends Widget
{
    public $data;

    public function run()
    {
        return Html::tag(
            'div',
            implode('', array_map(
                function (SalmonSchedule2 $schedule): string {
                    return $this->renderSchedule($schedule);
                },
                $this->data['data']
            )),
            ['class' => 'row']
        );
    }

    public function renderSchedule(SalmonSchedule2 $schedule): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderHeading($schedule),
                $this->renderBody($schedule),
            ]),
            ['class' => [
                'col-xs-12',
                'col-md-6',
            ]]
        );
    }

    public function renderHeading(SalmonSchedule2 $schedule): string
    {
        $fmt = Yii::$app->formatter;
        return Html::tag('h3', Html::encode(vsprintf('[%s - %s]', [
            $fmt->asDateTime($schedule->start_at, 'short'),
            $fmt->asDateTime($schedule->end_at, 'short'),
        ])));
    }

    public function renderBody(SalmonSchedule2 $schedule): string
    {
        return Html::tag(
            'ul',
            Html::tag('li', $this->renderScheduleData($schedule)),
            ['class' => [
                'battles',
                'maps',
                'salmon-schedule',
            ]]
        );
    }

    public function renderScheduleData(SalmonSchedule2 $schedule): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    $this->renderImage($schedule->map),
                    $this->renderWeapons(array_slice(
                        array_merge($schedule->weapons, [null, null, null, null]),
                        0,
                        4
                    )),
                ]),
                ['class' => 'row']
            ),
            ['class' => [
                'thumbnail',
                'thumbnail-salmon',
            ]]
        );
    }

    public function renderImage(SalmonMap2 $map): string
    {
        return Html::tag(
            'div',
            implode('', [
                Spl2Stage::img('daytime', $map->key, [
                    'class' => 'img-responsive',
                ]),
                Html::tag(
                    'div',
                    Html::encode(Yii::t('app-salmon-map2', $map->name)),
                    ['class' => 'battle-data']
                ),
            ]),
            ['class' => 'col-xs-6']
        );
    }

    public function renderWeapons(array $weapons): string
    {
        $icons = Spl2WeaponAsset::register($this->view);
        return Html::tag(
            'div',
            Html::tag('ul', implode('', array_map(
                function (?SalmonWeapon2 $weapon) use ($icons): string {
                    return Html::tag(
                        'li',
                        $weapon
                            ? implode(' ', [
                                Html::img($icons->getIconUrl($weapon->weapon->key), ['class' => [
                                    'w-auto',
                                    'h-em',
                                ]]),
                                Html::encode(Yii::t('app-weapon2', $weapon->weapon->name)),
                            ])
                            : Html::encode(Yii::t('app-salmon2', 'Random'))
                    );
                },
                $weapons
            ))),
            ['class' => 'col-xs-6']
        );
    }
}
