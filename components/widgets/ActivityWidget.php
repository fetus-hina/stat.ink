<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\BabelPolyfillAsset;
use statink\yii2\calHeatmap\CalHeatmapWidget;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class ActivityWidget extends CalHeatmapWidget
{
    public $user;
    public $only;
    public $months = 12;
    public $longLabel = true;
    public $size = 10;

    public function init()
    {
        parent::init();

        $this->options = [
            'style' => 'padding:5px 2px;',
        ];

        $this->jsOptions = [
            'data' => Url::to(array_filter(['api-internal/activity',
                'screen_name' => $this->user->screen_name,
                'only' => $this->only,
            ])),
            'afterLoadData' => $this->renderDataConverter(),
            'range' => $this->months,
            'start' => $this->renderStartTime(),
            'itemName' => [
                Yii::t('app', '{n,plural,=1{battle} other{battles}}', ['n' => 1]),
                Yii::t('app', '{n,plural,=1{battle} other{battles}}', ['n' => 42]),
            ],
            'legendTitleFormat' => [
                'lower' => Yii::t('app', 'less than {min} {name}'),
                'inner' => Yii::t('app', 'between {down} and {up} {name}'),
                'upper' => Yii::t('app', 'more than {max} {name}'),
            ],
            'domainLabelFormat' => $this->renderMonthLabels(),
            'subDomainTitleFormat' => [
                'empty' => '{date}: ' . Yii::t('app', 'No battles'),
                'filled' => '{date}: {count} {name}',
            ],
            'subDomainDateFormat' => Yii::t('app', '%m/%d/%Y'),
            'displayLegend' => false,
            'cellSize' => $this->size,
        ];
    }

    protected function renderDataConverter(): JsExpression
    {
        BabelPolyfillAsset::register($this->view);

        //  data => {
        //    const result = {};
        //    data.forEach(d => {
        //      const t = new Date(d.date);
        //      result[t.getTime() / 1000] = d.count;
        //    });
        //    return result;
        // }

        return new JsExpression(
            'function(a){var b={};return a.forEach(function(c){' .
            'var e=new Date(c.date);b[e.getTime()/1e3]=c.count}),b}'
        );
    }

    protected function renderStartTime(): JsExpression
    {
        $today = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp(time())
            ->setTime(0, 0, 0);

        $date = $today->setDate(
            (int)$today->format('Y'),
            (int)$today->format('n') - $this->months + 1,
            1
        );

        return new JsExpression(sprintf(
            'new Date(%s)',
            Json::encode($date->format('Y-m-d'))
        ));
    }

    protected function renderMonthLabels(): JsExpression
    {
        $f = Yii::$app->formatter;

        return new JsExpression(sprintf(
            'function(d){return %s[d.getMonth()]}',
            Json::encode(array_map(
                function (int $m) use ($f): string {
                    // LLLL: "January", LLL: "Jan"
                    return $f->asDate(
                        sprintf('2001-%02d-01', $m),
                        $this->longLabel ? 'LLLL' : 'LLL'
                    );
                },
                range(1, 12)
            ))
        ));
    }
}
