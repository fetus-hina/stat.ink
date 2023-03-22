<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\web\Application;

// https://github.com/fetus-hina/stat.ink/pull/1211
//
// アプリケーション設定の aliases は最初に設定されるのに対して、
// @web の設定は `yii\web\Application::bootstrap()` で行われるため
// @web を利用する alias の設定をアプリケーション設定で行うと
// 「未定義のalias」を参照するため、上記PRで指摘されているエラーが発生する。
// そこで、bootstrap としてこの登録を行うことで @web が登録されるまで遅延させる
return function (Application $app): bool {
    $useImgStatInk = ArrayHelper::getValue($app->params, 'useImgStatInk', false);

    Yii::setAlias(
        '@imageurl',
        $useImgStatInk ? 'https://img.stat.ink' : '@web/images',
    );

    return false;
};
