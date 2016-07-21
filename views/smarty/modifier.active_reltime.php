<?php
use app\assets\ActiveReltimeAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

if (!function_exists('smarty_modifier_relative_time')) {
    require_once(__DIR__ . '/modifier.relative_time.php');
}

function smarty_modifier_active_reltime($string, $mode = 'long')
{
    $time = \Yii::$app->formatter->asTimestamp($string);
    $text = smarty_modifier_relative_time($string, $mode);

    $l10n = [
        'short' => [
            'one' => [
                'year' => \Yii::t('app-reltime', '{delta} yr', ['delta' => 1]),
                'mon'  => \Yii::t('app-reltime', '{delta} mo', ['delta' => 1]),
                'day'  => \Yii::t('app-reltime', '{delta} d', ['delta' => 1]),
                'hour' => \Yii::t('app-reltime', '{delta} h', ['delta' => 1]),
                'min'  => \Yii::t('app-reltime', '{delta} m', ['delta' => 1]),
                'sec'  => \Yii::t('app-reltime', '{delta} s', ['delta' => 1]),
                'now'  => \Yii::t('app-reltime', 'now'),
            ],
            'many' => [
                'year' => \Yii::t('app-reltime', '{delta} yr', ['delta' => 42]),
                'mon'  => \Yii::t('app-reltime', '{delta} mo', ['delta' => 42]),
                'day'  => \Yii::t('app-reltime', '{delta} d', ['delta' => 42]),
                'hour' => \Yii::t('app-reltime', '{delta} h', ['delta' => 42]),
                'min'  => \Yii::t('app-reltime', '{delta} m', ['delta' => 42]),
                'sec'  => \Yii::t('app-reltime', '{delta} s', ['delta' => 42]),
                'now'  => \Yii::t('app-reltime', 'now'),
            ],
        ],
        'long' => [
            'one' => [
                'year' => \Yii::t('yii', '{delta, plural, =1{a year} other{# years}} ago', ['delta' => 1]),
                'mon'  => \Yii::t('yii', '{delta, plural, =1{a month} other{# months}} ago', ['delta' => 1]),
                'day'  => \Yii::t('yii', '{delta, plural, =1{a day} other{# days}} ago', ['delta' => 1]),
                'hour' => \Yii::t('yii', '{delta, plural, =1{an hour} other{# hours}} ago', ['delta' => 1]),
                'min'  => \Yii::t('yii', '{delta, plural, =1{a minute} other{# minutes}} ago', ['delta' => 1]),
                'sec'  => \Yii::t('yii', '{delta, plural, =1{a second} other{# seconds}} ago', ['delta' => 1]),
                'now'  => \Yii::t('yii', 'just now'),
            ],
            'many' => [
                'year' => \Yii::t('yii', '{delta, plural, =1{a year} other{# years}} ago', ['delta' => 42]),
                'mon'  => \Yii::t('yii', '{delta, plural, =1{a month} other{# months}} ago', ['delta' => 42]),
                'day'  => \Yii::t('yii', '{delta, plural, =1{a day} other{# days}} ago', ['delta' => 42]),
                'hour' => \Yii::t('yii', '{delta, plural, =1{an hour} other{# hours}} ago', ['delta' => 42]),
                'min'  => \Yii::t('yii', '{delta, plural, =1{a minute} other{# minutes}} ago', ['delta' => 42]),
                'sec'  => \Yii::t('yii', '{delta, plural, =1{a second} other{# seconds}} ago', ['delta' => 42]),
                'now'  => \Yii::t('yii', 'just now'),
            ],
        ],
    ];

    $view = \Yii::$app->controller->view;
    ActiveReltimeAsset::register($view);
    $view->registerJs(
        sprintf(
            'window.reltimeFormats = %s;',
            Json::encode($l10n)
        ),
        View::POS_BEGIN,
        hash('md5', sprintf('%s:%s:%d', __METHOD__, __FILE__, __LINE__))
    );

    
    return Html::tag(
        'span',
        Html::encode(smarty_modifier_relative_time($string, $mode)),
        [
            'class' => 'active-reltime',
            'data' => [
                'time' => (string)(int)$time,
                'mode' => $mode === 'short' ? 'short' : 'long',
            ]
        ]
    );
}
