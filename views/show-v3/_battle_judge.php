<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->registerCss('.cell-judge .progress{width:100%;min-width:150px;margin-bottom:0}');

$draw = function (
    float $lhsValue,
    float $rhsValue,
    string $lhsString,
    string $rhsString,
    ?string $lhsTitle = null,
    ?string $rhsTitle = null
): string {
    $bar = function (
        float $curValue,
        float $otherValue,
        string $class,
        string $string,
        ?string $title = null
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
if ($model->our_team_percent !== null && $model->their_team_percent !== null) {
    $myTitle = null;
    $hisTitle = null;
    if ($model->our_team_inked !== null && $model->their_team_inked !== null) {
        $myTitle = Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->our_team_inked),
        ]);
        $hisTitle = Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->their_team_inked),
        ]);
    } elseif ($model->map && $model->map->area !== null) {
        $myTitle = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round(
                $model->our_team_percent * $model->map->area / 100
            )),
        ]);
        $hisTitle = Yii::t('app', '~{point}p', [
            'point' => Yii::$app->formatter->asInteger(round(
                $model->their_team_percent * $model->map->area / 100
            )),
        ]);
    }
    $v = $draw(
        $model->our_team_percent,
        $model->their_team_percent,
        $fmt->asPercent($model->our_team_percent / 100, 1),
        $fmt->asPercent($model->their_team_percent / 100, 1),
        $myTitle,
        $hisTitle
    );
    echo "$v\n";
} elseif ($model->our_team_inked !== null && $model->their_team_inked !== null) {
    $v = $draw(
        $model->our_team_inked,
        $model->their_team_inked,
        Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->our_team_inked),
        ]),
        Yii::t('app', '{point}p', [
            'point' => Yii::$app->formatter->asInteger((int)$model->their_team_inked),
        ])
    );
    echo "$v\n";
} elseif ($model->our_team_count !== null && $model->their_team_count !== null) {
    $v = $draw(
        $model->our_team_count,
        $model->their_team_count,
        $model->our_team_count == 100
          ? Yii::t('app', 'KNOCKOUT')
          : (string)$model->our_team_count,
        $model->their_team_count == 100
          ? Yii::t('app', 'KNOCKOUT')
          : (string)$model->their_team_count
    );
    echo "$v\n";
}
