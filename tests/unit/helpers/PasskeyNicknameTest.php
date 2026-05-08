<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use DateTimeImmutable;
use Yii;
use app\components\helpers\PasskeyNickname;

use function mb_strlen;
use function str_repeat;

class PasskeyNicknameTest extends Unit
{
    /**
     * @dataProvider isKnownAaguidDataProvider
     */
    public function testIsKnownAaguid(string $aaguid, bool $expected): void
    {
        $this->assertSame($expected, PasskeyNickname::isKnownAaguid($aaguid));
    }

    public function isKnownAaguidDataProvider(): array
    {
        return [
            'real aaguid (lowercase)' => [
                'cb69481e-8ff7-4039-93ec-0a2729a154a8',
                true,
            ],
            'real aaguid (uppercase)' => [
                'CB69481E-8FF7-4039-93EC-0A2729A154A8',
                true,
            ],
            'all-zero (lowercase)' => [
                '00000000-0000-0000-0000-000000000000',
                false,
            ],
            'all-zero (with whitespace)' => [
                '  00000000-0000-0000-0000-000000000000  ',
                false,
            ],
        ];
    }

    public function testBuildDefaultUsesAaguidName(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $this->assertSame(
            'YubiKey 5 NFC',
            PasskeyNickname::buildDefault(
                'cb69481e-8ff7-4039-93ec-0a2729a154a8',
                'YubiKey 5 NFC',
                $now,
                'Passkey ({date})',
            ),
        );
    }

    public function testBuildDefaultTrimsAaguidName(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $this->assertSame(
            'YubiKey 5 NFC',
            PasskeyNickname::buildDefault(
                'cb69481e-8ff7-4039-93ec-0a2729a154a8',
                "  YubiKey 5 NFC  \n",
                $now,
                'Passkey ({date})',
            ),
        );
    }

    public function testBuildDefaultTruncatesLongAaguidName(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $longName = str_repeat('A', 100);
        $this->assertSame(
            str_repeat('A', 64),
            PasskeyNickname::buildDefault(
                'cb69481e-8ff7-4039-93ec-0a2729a154a8',
                $longName,
                $now,
                'Passkey ({date})',
            ),
        );
    }

    public function testBuildDefaultTruncatesByCharacterForMultibyte(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        // 80 Japanese characters — should be truncated to 64 *characters*, not bytes.
        $longName = str_repeat('あ', 80);
        $result = PasskeyNickname::buildDefault(
            'cb69481e-8ff7-4039-93ec-0a2729a154a8',
            $longName,
            $now,
            'パスキー ({date})',
        );
        $this->assertSame(str_repeat('あ', 64), $result);
        $this->assertSame(64, mb_strlen($result, 'UTF-8'));
    }

    public function testBuildDefaultFallsBackOnNullName(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $result = $this->withFormatterLocale('ja', static fn (): string => PasskeyNickname::buildDefault(
            'cb69481e-8ff7-4039-93ec-0a2729a154a8',
            null,
            $now,
            'パスキー ({date})',
        ));
        // Should start with the template prefix and contain the formatted date.
        $this->assertStringStartsWith('パスキー (', $result);
        $this->assertStringEndsWith(')', $result);
    }

    public function testBuildDefaultFallsBackOnEmptyName(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $result = $this->withFormatterLocale('en-US', static fn (): string => PasskeyNickname::buildDefault(
            'cb69481e-8ff7-4039-93ec-0a2729a154a8',
            '   ',
            $now,
            'Passkey ({date})',
        ));
        $this->assertStringStartsWith('Passkey (', $result);
        $this->assertStringEndsWith(')', $result);
    }

    public function testBuildDefaultFallsBackOnZeroAaguid(): void
    {
        // Even if a name is provided, an all-zero aaguid means we cannot reliably
        // identify the authenticator; fall back to the date-based default.
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $result = $this->withFormatterLocale('en-US', static fn (): string => PasskeyNickname::buildDefault(
            '00000000-0000-0000-0000-000000000000',
            'Some Authenticator',
            $now,
            'Passkey ({date})',
        ));
        $this->assertStringStartsWith('Passkey (', $result);
        $this->assertStringNotContainsString('Some Authenticator', $result);
    }

    public function testBuildDefaultUsesLocaleSpecificDate(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $aaguid = '00000000-0000-0000-0000-000000000000';
        $template = 'Passkey ({date})';

        $resultJa = $this->withFormatterLocale('ja', static fn (): string => PasskeyNickname::buildDefault($aaguid, null, $now, $template));
        $resultDe = $this->withFormatterLocale('de', static fn (): string => PasskeyNickname::buildDefault($aaguid, null, $now, $template));
        $resultEn = $this->withFormatterLocale('en-US', static fn (): string => PasskeyNickname::buildDefault($aaguid, null, $now, $template));

        $this->assertNotSame($resultJa, $resultDe);
        $this->assertNotSame($resultJa, $resultEn);
        $this->assertNotSame($resultDe, $resultEn);

        // German short date uses dots ("08.05.26"); US English uses slashes ("5/8/26").
        $this->assertStringContainsString('.', $resultDe);
        $this->assertStringContainsString('/', $resultEn);
    }

    public function testBuildDefaultTruncatesDateFallback(): void
    {
        $now = new DateTimeImmutable('2026-05-08T12:00:00+09:00');
        $longTemplate = str_repeat('X', 100) . ' ({date})';
        $result = PasskeyNickname::buildDefault(
            'cb69481e-8ff7-4039-93ec-0a2729a154a8',
            null,
            $now,
            $longTemplate,
        );
        $this->assertSame(64, mb_strlen($result, 'UTF-8'));
        $this->assertSame(str_repeat('X', 64), $result);
    }

    /**
     * Run a callable with Yii::$app->formatter temporarily switched to the given locale,
     * restoring the original locale afterwards regardless of the outcome.
     *
     * @param callable(): string $fn
     */
    private function withFormatterLocale(string $locale, callable $fn): string
    {
        $f = Yii::$app->formatter;
        $savedLocale = $f->locale;
        $savedLanguage = $f->language;
        try {
            $f->locale = $locale;
            $f->language = $locale;
            return $fn();
        } finally {
            $f->locale = $savedLocale;
            $f->language = $savedLanguage;
        }
    }
}
