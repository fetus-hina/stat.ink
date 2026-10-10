<?php

declare(strict_types=1);

namespace tests\i18n;

use Codeception\Test\Unit;
use app\components\i18n\TranslationKeyChecker;

class TranslationKeyCheckerTest extends Unit
{
    private const CATALOGS = [
        'app' => ['Hello' => 'こんにちは', 'Untranslated' => ''],
        'app-email' => ['Logged in' => 'ログインしました'],
        'app-missing-file' => null,
    ];

    public function testReturnsNothingWhenAllKeysExist(): void
    {
        $this->assertSame([], TranslationKeyChecker::findMissing(
            [
                ['category' => 'app', 'message' => 'Hello', 'line' => 1],
                ['category' => 'app-email', 'message' => 'Logged in', 'line' => 2],
            ],
            self::CATALOGS,
        ));
    }

    public function testKeyWithEmptyTranslationCountsAsExisting(): void
    {
        $this->assertSame([], TranslationKeyChecker::findMissing(
            [['category' => 'app', 'message' => 'Untranslated', 'line' => 1]],
            self::CATALOGS,
        ));
    }

    public function testReportsMissingKey(): void
    {
        $this->assertSame(
            [['category' => 'app', 'message' => 'Goodbye', 'line' => 3, 'reason' => TranslationKeyChecker::REASON_MISSING_KEY]],
            TranslationKeyChecker::findMissing(
                [
                    ['category' => 'app', 'message' => 'Hello', 'line' => 1],
                    ['category' => 'app', 'message' => 'Goodbye', 'line' => 3],
                ],
                self::CATALOGS,
            ),
        );
    }

    public function testKeyIsCaseSensitive(): void
    {
        $this->assertCount(1, TranslationKeyChecker::findMissing(
            [['category' => 'app', 'message' => 'hello', 'line' => 1]],
            self::CATALOGS,
        ));
    }

    public function testReportsMissingCatalog(): void
    {
        $this->assertSame(
            [['category' => 'app-missing-file', 'message' => 'Hello', 'line' => 1, 'reason' => TranslationKeyChecker::REASON_MISSING_CATALOG]],
            TranslationKeyChecker::findMissing(
                [['category' => 'app-missing-file', 'message' => 'Hello', 'line' => 1]],
                self::CATALOGS,
            ),
        );
    }

    public function testSkipsCategoriesNotInCatalogMap(): void
    {
        $this->assertSame([], TranslationKeyChecker::findMissing(
            [
                ['category' => 'yii', 'message' => 'Error', 'line' => 1],
                ['category' => 'db-weapon', 'message' => 'Splattershot', 'line' => 2],
            ],
            self::CATALOGS,
        ));
    }

    public function testKeepsExtraFieldsOfCall(): void
    {
        $result = TranslationKeyChecker::findMissing(
            [['category' => 'app', 'message' => 'Goodbye', 'line' => 3, 'file' => 'views/a.php']],
            self::CATALOGS,
        );

        $this->assertSame('views/a.php', $result[0]['file']);
    }
}
