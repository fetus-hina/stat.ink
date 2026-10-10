<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use Override;
use Yii;
use app\components\helpers\UserAgentHelper;

class UserAgentHelperTest extends Unit
{
    private const string UA_IPHONE = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

    private string $savedLanguage;

    #[Override]
    protected function _before(): void
    {
        $this->savedLanguage = Yii::$app->language;
    }

    #[Override]
    protected function _after(): void
    {
        Yii::$app->language = $this->savedLanguage;
    }

    public function testNullUserAgentReturnsDefault(): void
    {
        $this->assertNull(UserAgentHelper::summary(null));
        $this->assertSame('default', UserAgentHelper::summary(null, 'default'));
    }

    public function testEmptyUserAgentReturnsDefault(): void
    {
        $this->assertSame('default', UserAgentHelper::summary('', 'default'));
    }

    public function testSummaryUsesGivenLanguage(): void
    {
        Yii::$app->language = 'ja-JP';

        $this->assertSame(
            'Safari / iOS 17.0 / iPhone (Mobile)',
            UserAgentHelper::summary(self::UA_IPHONE, null, 'en-US'),
        );
    }

    public function testSummaryTranslatesDeviceTypePerLanguage(): void
    {
        Yii::$app->language = 'en-US';

        $this->assertSame(
            'Safari / iOS 17.0 / iPhone (モバイル)',
            UserAgentHelper::summary(self::UA_IPHONE, null, 'ja-JP'),
        );
        $this->assertSame(
            'Safari / iOS 17.0 / iPhone (Mobil)',
            UserAgentHelper::summary(self::UA_IPHONE, null, 'de-DE'),
        );
    }

    public function testSummaryDefaultsToAppLanguage(): void
    {
        Yii::$app->language = 'ja-JP';

        $this->assertSame(
            'Safari / iOS 17.0 / iPhone (モバイル)',
            UserAgentHelper::summary(self::UA_IPHONE),
        );
    }
}
