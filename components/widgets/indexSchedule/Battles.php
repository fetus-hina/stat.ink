<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\indexSchedule;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\widgets\GameModeIcon;
use app\models\Map2;
use app\models\Rule2;
use statink\yii2\stages\spl2\Spl2Stage;
use stdClass;
use yii\base\Widget;
use yii\helpers\Html;

class Battles extends Widget
{
    public $data;

    public function run()
    {
        return Html::tag(
            'div',
            implode('', array_map(
                function (?stdClass $schedule): string {
                    if (!$schedule || !$schedule->data || !$schedule->term) {
                        return '';
                    }

                    return Html::tag(
                        'div',
                        implode('', [
                            $this->renderHeading($schedule),
                            $this->renderMaps($schedule->data->rule, $schedule->data->maps),
                        ]),
                        ['class' => [
                            'col-xs-12',
                            'col-md-6',
                        ]]
                    );
                },
                $this->data['data'],
            )),
            ['class' => 'row']
        );
    }

    public function renderHeading(stdClass $schedule): string
    {
        $t1 = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($schedule->term[0]);

        $t2 = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($schedule->term[1]);

        $timeFormat = (strtolower(Yii::$app->language) === 'en-us')
            ? 'g a'
            : 'H:i';

        return Html::tag('h3', implode(' ', [
          Html::encode(vsprintf('[%s-%s]', [
            $t1->format($timeFormat),
            $t2->format($timeFormat),
          ])),
          GameModeIcon::spl2($schedule->data->rule->key),
          Html::encode(Yii::t('app-rule2', $schedule->data->rule->name)),
        ]));
    }

    public function renderMaps(Rule2 $rule, array $maps): string
    {
        return Html::tag(
            'ul',
            implode('', array_map(
                function (Map2 $map) use ($rule): string {
                    return Html::tag(
                        'li',
                        $this->renderMap($rule, $map)
                    );
                },
                $maps
            )),
            ['class' => [
                'battles',
                'maps',
            ]]
        );
    }

    public function renderMap(Rule2 $rule, Map2 $map): string
    {
        return Html::tag(
            'div',
            implode('', [
                Spl2Stage::img('daytime', $map->key),
                Html::tag(
                    'div',
                    Html::encode(Yii::t('app-map2', $map->name)),
                    ['class' => 'battle-data']
                ),
            ]),
            ['class' => [
                'thumbnail',
                'thumbnail-' . $rule->key,
            ]]
        );
    }
}
