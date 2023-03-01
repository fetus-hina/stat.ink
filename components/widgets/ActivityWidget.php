<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\User;
use statink\yii2\calHeatmap\CalHeatmapWidget;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

use function array_filter;
use function preg_replace;
use function sprintf;
use function strtolower;
use function time;

final class ActivityWidget extends CalHeatmapWidget
{
    public ?User $user = null;
    public ?string $only = null;
    public int $months = 12;
    public bool $longLabel = true;
    public int $size = 10;

    public function init()
    {
        parent::init();

        $this->options = [
            'style' => 'padding:5px 2px;',
        ];

        if (!$user = $this->user) {
            throw new InvalidConfigException();
        }

        $view = $this->view;
        // if ($view instanceof View) {
        //     CalHeatmapLegendAsset::register($view);
        //     CalHeatmapTooltipAsset::register($view);
        // }

        $apiUrl = Url::to(
            array_filter(
                ['api-internal/activity',
                    'screen_name' => $user->screen_name,
                    'only' => $this->only,
                ],
            ),
        );
        $this->jsOptions = [
            'data' => [
                'source' => $apiUrl,
                'type' => 'json',
                'x' => $this->renderDataConverterX(),
                'y' => $this->renderDataConverterY(),
            ],
            'date' => [
                'start' => $this->renderStartTime(),
                'locale' => preg_replace( // workaround for #1202
                    '/^([a-z]+)-.+$/',
                    '$1',
                    self::getDayjsLocaleName(strtolower((string)Yii::$app->language)),
                ),
            ],
            'range' => $this->months,
            'scale' => [
                'color' => [
                    'domain' => [0, 30],
                    'scheme' => $this->isHalloweenTerm() ? 'Oranges' : 'Greens',
                    'type' => 'linear',
                ],
            ],
            'subDomain' => [
                'height' => $this->size,
                'width' => $this->size,
            ],
            // 'itemName' => [
            //     Yii::t('app', '{n,plural,=1{battle} other{battles}}', ['n' => 1]),
            //     Yii::t('app', '{n,plural,=1{battle} other{battles}}', ['n' => 42]),
            // ],
            // 'legendTitleFormat' => [
            //     'lower' => Yii::t('app', 'less than {min} {name}'),
            //     'inner' => Yii::t('app', 'between {down} and {up} {name}'),
            //     'upper' => Yii::t('app', 'more than {max} {name}'),
            // ],
            // 'subDomainTitleFormat' => [
            //     'empty' => '{date}: ' . Yii::t('app', 'No battles'),
            //     'filled' => '{date}: {count} {name}',
            // ],
            // 'displayLegend' => false,
        ];
    }

    private function renderDataConverterX(): JsExpression
    {
        return new JsExpression(
            'function(e){return new Date(e.date).getTime()}',
        );
    }

    private function renderDataConverterY(): JsExpression
    {
        return new JsExpression(
            'function(e){return e.count}',
        );
    }

    protected function renderStartTime(): JsExpression
    {
        $today = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
            ->setTime(0, 0, 0);

        $date = $today->setDate(
            (int)$today->format('Y'),
            (int)$today->format('n') - $this->months + 1,
            1,
        );

        return new JsExpression(sprintf(
            'new Date(%s)',
            Json::encode($date->format('Y-m-d')),
        ));
    }

    private function isHalloweenTerm(): bool
    {
        $now = (new DateTimeImmutable())
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));
        $month = (int)$now->format('n');
        $day = (int)$now->format('j');

        return ($month === 10 && $day > 24) || ($month === 11 && $day === 1);
    }
}
