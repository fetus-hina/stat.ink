<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets;

use Yii;
use app\assets\ActiveReltimeAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

use function floor;
use function hash;
use function sprintf;
use function time;

class ActiveRelativeTimeWidget extends Widget
{
    public $datetime;
    public $mode = 'long';

    public function run()
    {
        ActiveReltimeAsset::register($this->view);
        $this->view->registerJs(
            sprintf(
                '!function(){window.reltimeFormats=%s}();',
                Json::encode($this->getFormats()),
            ),
            View::POS_BEGIN,
            hash('md5', sprintf('%s:%s:%d', __METHOD__, __FILE__, __LINE__)),
        );
        return Html::tag(
            'span',
            Html::encode($this->formatReltime()),
            [
                'class' => 'active-reltime',
                'data' => [
                    'time' => (string)(int)$this->datetime->getTimestamp(),
                    'mode' => $this->mode === 'short' ? 'short' : 'long',
                ],
            ],
        );
    }

    protected function formatReltime(): string
    {
        $formatter = Yii::$app->getFormatter();
        $now = $_SERVER['REQUEST_TIME'] ?? time();

        switch ($this->mode) {
            case 'long':
            default:
                return $formatter->asRelativeTime($this->datetime, $now);

            case 'short':
                $diff = $now - $this->datetime->getTimestamp();
                if ($diff >= 31536000) {
                    return Yii::t('app-reltime', '{delta} yr', ['delta' => (int)floor($diff / 31536000)]);
                } elseif ($diff >= 2592000) {
                    return Yii::t('app-reltime', '{delta} mo', ['delta' => (int)floor($diff / 2592000)]);
                } elseif ($diff >= 86400) {
                    return Yii::t('app-reltime', '{delta} d', ['delta' => (int)floor($diff / 86400)]);
                } elseif ($diff >= 3600) {
                    return Yii::t('app-reltime', '{delta} h', ['delta' => (int)floor($diff / 3600)]);
                } elseif ($diff >= 60) {
                    return Yii::t('app-reltime', '{delta} m', ['delta' => (int)floor($diff / 60)]);
                } elseif ($diff >= 15) {
                    return Yii::t('app-reltime', '{delta} s', ['delta' => (int)$diff]);
                } else {
                    return Yii::t('app-reltime', 'now');
                }
        }
    }

    protected function getFormats(): array
    {
        // {{{
        return [
            'short' => [
                'one' => [
                    'year' => Yii::t('app-reltime', '{delta} yr', ['delta' => 1]),
                    'mon' => Yii::t('app-reltime', '{delta} mo', ['delta' => 1]),
                    'day' => Yii::t('app-reltime', '{delta} d', ['delta' => 1]),
                    'hour' => Yii::t('app-reltime', '{delta} h', ['delta' => 1]),
                    'min' => Yii::t('app-reltime', '{delta} m', ['delta' => 1]),
                    'sec' => Yii::t('app-reltime', '{delta} s', ['delta' => 1]),
                    'now' => Yii::t('app-reltime', 'now'),
                ],
                'many' => [
                    'year' => Yii::t('app-reltime', '{delta} yr', ['delta' => 42]),
                    'mon' => Yii::t('app-reltime', '{delta} mo', ['delta' => 42]),
                    'day' => Yii::t('app-reltime', '{delta} d', ['delta' => 42]),
                    'hour' => Yii::t('app-reltime', '{delta} h', ['delta' => 42]),
                    'min' => Yii::t('app-reltime', '{delta} m', ['delta' => 42]),
                    'sec' => Yii::t('app-reltime', '{delta} s', ['delta' => 42]),
                    'now' => Yii::t('app-reltime', 'now'),
                ],
            ],
            'long' => [
                'one' => [
                    'year' => Yii::t('yii', '{delta, plural, =1{a year} other{# years}} ago', ['delta' => 1]),
                    'mon' => Yii::t('yii', '{delta, plural, =1{a month} other{# months}} ago', ['delta' => 1]),
                    'day' => Yii::t('yii', '{delta, plural, =1{a day} other{# days}} ago', ['delta' => 1]),
                    'hour' => Yii::t('yii', '{delta, plural, =1{an hour} other{# hours}} ago', ['delta' => 1]),
                    'min' => Yii::t('yii', '{delta, plural, =1{a minute} other{# minutes}} ago', ['delta' => 1]),
                    'sec' => Yii::t('yii', '{delta, plural, =1{a second} other{# seconds}} ago', ['delta' => 1]),
                    'now' => Yii::t('yii', 'just now'),
                ],
                'many' => [
                    'year' => Yii::t('yii', '{delta, plural, =1{a year} other{# years}} ago', ['delta' => 42]),
                    'mon' => Yii::t('yii', '{delta, plural, =1{a month} other{# months}} ago', ['delta' => 42]),
                    'day' => Yii::t('yii', '{delta, plural, =1{a day} other{# days}} ago', ['delta' => 42]),
                    'hour' => Yii::t('yii', '{delta, plural, =1{an hour} other{# hours}} ago', ['delta' => 42]),
                    'min' => Yii::t('yii', '{delta, plural, =1{a minute} other{# minutes}} ago', ['delta' => 42]),
                    'sec' => Yii::t('yii', '{delta, plural, =1{a second} other{# seconds}} ago', ['delta' => 42]),
                    'now' => Yii::t('yii', 'just now'),
                ],
            ],
        ];
        // }}}
    }
}
