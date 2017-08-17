<?php
use yii\helpers\Html;

$this->registerCss('.cell-judge .progress{width:100%;min-width:150px;margin-bottom:0}');

$draw = function (float $lhsValue, float $rhsValue, string $lhsString, string $rhsString) : string {
    $bar = function (float $curValue, float $otherValue, string $class, string $string) : string {
        return ($curValue > 0)
            ? Html::tag('div', Html::encode($string), [
                'class' => ['progress-bar', $class],
                'style' => [
                    'width' => sprintf('%.f%%', $curValue * 100 / ($curValue + $otherValue)),
                ],
            ])
            : '';
    };
    return Html::tag(
        'div',
        implode('', [
            $lhsValue > 0
                ? $bar($lhsValue, $rhsValue, 'progress-bar-info', $lhsString)
                : '',
            $rhsValue > 0
                ? $bar($rhsValue, $lhsValue, 'progress-bar-danger', $rhsString)
                : '',
        ]),
        ['class' => 'progress']
    );
};

$fmt = Yii::$app->formatter;
if ($model->my_team_percent !== null && $model->his_team_percent !== null) {
    $v = $draw(
        $model->my_team_percent,
        $model->his_team_percent,
        $fmt->asPercent($model->my_team_percent / 100, 1),
        $fmt->asPercent($model->his_team_percent / 100, 1)
    );
    echo "$v\n";
} elseif ($model->my_team_point !== null && $model->his_team_point !== null) {
    $v = $draw(
        $model->my_team_point,
        $model->his_team_point,
        Yii::t('app', '{point}p', ['point' => (int)$model->my_team_point]),
        Yii::t('app', '{point}p', ['point' => (int)$model->his_team_point])
    );
    echo "$v\n";
} elseif ($model->my_team_count !== null && $model->his_team_count !== null) {
    $v = $draw(
        $model->my_team_count,
        $model->his_team_count,
        $model->my_team_count,
        $model->his_team_count
    );
    echo "$v\n";
}
