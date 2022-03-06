<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use Yii;
use app\components\helpers\AssetHashHelper;

final class AssetHashHelperTest extends Unit
{
    public function testCalc(): void
    {
        Yii::$app->params['assetRevision'] = 42;
        Yii::$app->params['gitRevision.lastCommittedT'] = strtotime('2022-03-06T01:02:03+00:00');

        $path = AssetHashHelper::calc((string)Yii::getAlias('@app/runtime'));
        $this->assertIsString($path);
        $this->assertMatchesRegularExpression(
            '#^20220306-42/[a-z2-7]+$#',
            $path,
        );
    }
}
