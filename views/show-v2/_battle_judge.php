<?php

use app\models\Battle2;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle2 $model
 * @var View $this
 */

$this->registerCss('.cell-judge .progress{width:100%;min-width:150px;margin-bottom:0}');

$draw = function (
    float $lhsValue,
    float $rhsValue,
    string $lhsString,
    string $rhsString,
    ?string $lhsTitle = null,
    ?string $rhsTitle = null,
): string {
    $bar = function (
        float $curValue,
        float $otherValue,
        string $class,
        string $string,
        ?string $title = null,
    ): string {
        return ($curValue > 0)
            ? Html::tag('div', Html::encode($string), [
                'class' => [
                    'progress-bar',
                    $class,
                    $title === null ? '' : 'auto-tooltip',
                ],
                'style' => [
                    'width' => sprintf('%.f%%', $curValue * 100 / ($curValue + $otherValue)),
                    'font-weight' => ($curValue >= $otherValue) ? '700' : '400',
                ],
                'title' => $title ?? '',
            ])
            : '';
    };
    return Html::tag(
        'div',
        implode('', [
            $lhsValue > 0
                ? $bar($lhsValue, $rhsValue, 'progress-bar-info', $lhsString, $lhsTitle)
                : '',
            $rhsValue > 0
                ? $bar($rhsValue, $lhsValue, 'progress-bar-danger', $rhsString, $rhsTitle)
                : '',
        ]),
        ['class' => 'progress']
    );
};

$fmt = Yii::$app->formatter;
if ($model->my_team_percent !== null && $model->his_team_percent !== null) {
    $myTitle = null;
    $hisTitle = null;
    if ($model->my_team_point !== null && $model->his_team_point !== null) {
        $myTitle = Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->my_team_point),
        ]);
        $hisTitle = Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->his_team_point),
        ]);
    } elseif ($model->map && $model->map->area !== null) {
        $myTitle = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round(
                $model->my_team_percent * $model->map->area / 100
            )),
        ]);
        $hisTitle = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round(
                $model->his_team_percent * $model->map->area / 100
            )),
        ]);
    }
    $v = $draw(
        $model->my_team_percent,
        $model->his_team_percent,
        $fmt->asPercent($model->my_team_percent / 100, 1),
        $fmt->asPercent($model->his_team_percent / 100, 1),
        $myTitle,
        $hisTitle
    );
    echo "$v\n";
} elseif ($model->my_team_point !== null && $model->his_team_point !== null) {
    $v = $draw(
        $model->my_team_point,
        $model->his_team_point,
        Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->my_team_point),
        ]),
        Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->his_team_point),
        ])
    );
    echo "$v\n";
} elseif ($model->my_team_count !== null && $model->his_team_count !== null) {
    $v = $draw(
        $model->my_team_count,
        $model->his_team_count,
        $model->my_team_count == 100
          ? Yii::t('app', 'KNOCKOUT')
          : $model->my_team_count,
        $model->his_team_count == 100
          ? Yii::t('app', 'KNOCKOUT')
          : $model->his_team_count
    );
    echo "$v\n";
}
