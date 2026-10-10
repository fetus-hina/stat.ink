<?php

declare(strict_types=1);

namespace tests\i18n;

use Codeception\Test\Unit;
use app\components\i18n\IcuMessageChecker;

use function array_column;

class IcuMessageCheckerTest extends Unit
{
    public function testValidTranslation(): void
    {
        $this->assertSame([], IcuMessageChecker::check('Hello, {name}!', 'Hallo, {name}!', 'de'));
        $this->assertSame([], IcuMessageChecker::check('{0} etc.', '{0} usw.', 'de'));
        $this->assertSame([], IcuMessageChecker::check('No placeholders', 'Keine Platzhalter', 'de'));
    }

    public function testPluralWithNumber(): void
    {
        $this->assertSame([], IcuMessageChecker::check(
            '{n, plural, =1{1 battle} other{# battles}}',
            '{n, plural, one{# бой} few{# боя} many{# боёв} other{# боя}}',
            'ru',
        ));
    }

    public function testDroppedPluralWithoutNumberIsAllowed(): void
    {
        // The count is not displayed in the source either
        $this->assertSame([], IcuMessageChecker::check('{n,plural,=1{battle} other{battles}}', '对战', 'zh_CN'));
    }

    public function testInvalidPattern(): void
    {
        $this->assertSame(
            [IcuMessageChecker::INVALID_PATTERN],
            array_column(IcuMessageChecker::check('{name}: Hello', '{}: Bonjour', 'fr'), 'type'),
        );
    }

    public function testMissingArgument(): void
    {
        $result = IcuMessageChecker::check('{decimal5_7} Format', 'Formato', 'pt_BR');
        $this->assertSame([IcuMessageChecker::MISSING_ARGUMENT], array_column($result, 'type'));
        $this->assertStringContainsString('decimal5_7', $result[0]['detail']);
    }

    public function testMissingNumberedArgument(): void
    {
        $this->assertSame(
            [IcuMessageChecker::MISSING_ARGUMENT],
            array_column(IcuMessageChecker::check('{0} Fanboy', 'Novice', 'fr'), 'type'),
        );
    }

    public function testMissingNumberInPlural(): void
    {
        $this->assertSame(
            [IcuMessageChecker::MISSING_ARGUMENT],
            array_column(
                IcuMessageChecker::check('{n, plural, =1{1 battle} other{# battles}}', '{n, plural, =1{Kampf} other{Kämpfe}}', 'de'),
                'type',
            ),
        );
    }

    public function testUnresolvedPlaceholderByApostrophe(): void
    {
        // An apostrophe right before "{" quotes the placeholder in ICU
        $this->assertSame(
            [IcuMessageChecker::UNRESOLVED_PLACEHOLDER, IcuMessageChecker::MISSING_ARGUMENT],
            array_column(IcuMessageChecker::check('Edit {0}', "Modifier l'{0}", 'fr'), 'type'),
        );
    }

    public function testUnknownArgument(): void
    {
        $this->assertSame(
            [IcuMessageChecker::UNRESOLVED_PLACEHOLDER],
            array_column(IcuMessageChecker::check('Hello, {name}!', 'Hallo, {name} {nam}!', 'de'), 'type'),
        );
    }

    public function testIgnoredArguments(): void
    {
        $this->assertSame([], IcuMessageChecker::check('{boy}Apprentice', 'Praktikant', 'de'));
        $this->assertSame([], IcuMessageChecker::check('Mode{translate_hint_stats}', 'Modalwert', 'de'));
    }
}
