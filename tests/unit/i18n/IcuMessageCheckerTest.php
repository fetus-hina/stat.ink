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

    public function testMissingNonZeroNumberedArgument(): void
    {
        $this->assertSame(
            [IcuMessageChecker::MISSING_ARGUMENT],
            array_column(IcuMessageChecker::check('{1} Champion', 'Champion', 'ja'), 'type'),
        );
        $this->assertSame(
            [IcuMessageChecker::MISSING_ARGUMENT],
            array_column(IcuMessageChecker::check('{1, plural, =1{1 battle} other{# battles}}', '対戦', 'ja'), 'type'),
        );
    }

    public function testManyNumericArguments(): void
    {
        $source = '';
        for ($i = 0; $i < 20; $i++) {
            $source .= "{x{$i}, number} ";
        }
        $this->assertSame([], IcuMessageChecker::check($source, $source, 'de'));
    }

    public function testPluralValueAsInteger(): void
    {
        // 1.5 is shown as "2" by the integer format
        $this->assertSame([], IcuMessageChecker::check(
            '{n, plural, =1{1 battle} other{# battles}}',
            '{n, number, integer} 戦',
            'ja',
        ));
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

    public function testDecimalPluralValue(): void
    {
        // The plural value is also tested with a decimal, which fr renders as "1,5"
        $this->assertSame([], IcuMessageChecker::check(
            '{n, plural, =1{1 battle} other{# battles}}',
            '{n, plural, one{# combat} other{# combats}}',
            'fr',
        ));
    }

    public function testPluralValueAsSimpleArgument(): void
    {
        // "#" renders 1000000 as "1,000,000", while "{sec}" renders it as "1000000"
        $this->assertSame([], IcuMessageChecker::check('{sec,plural,=1{# second} other{# seconds}}', '{sec}秒', 'zh_CN'));
    }

    public function testUnknownArgumentInUnselectedPluralBranch(): void
    {
        // fr selects "one" for 0 and 1.5, which the default value does not
        $this->assertSame(
            [IcuMessageChecker::UNRESOLVED_PLACEHOLDER, IcuMessageChecker::MISSING_ARGUMENT],
            array_column(
                IcuMessageChecker::check(
                    '{n, plural, one{{pct_point} percentage point} other{{pct_point} percentage points}}',
                    '{n, plural, one{{pct_pt} point de pourcentage} other{{pct_point} points de pourcentage}}',
                    'fr',
                ),
                'type',
            ),
        );
    }

    public function testMissingNumberInUnselectedPluralBranch(): void
    {
        $result = IcuMessageChecker::check(
            '{n, plural, =1{1 battle} other{# battles}}',
            '{n, plural, one{бой} few{# боя} many{# боёв} other{# боя}}',
            'ru',
        );
        $this->assertSame([IcuMessageChecker::MISSING_ARGUMENT], array_column($result, 'type'));
        $this->assertStringContainsString('{n}', $result[0]['detail']);
    }

    public function testMissingNumberInOneOfPluralArguments(): void
    {
        $result = IcuMessageChecker::check(
            '{win, plural, =1{1 win} other{# wins}} / {lose, plural, =1{1 loss} other{# losses}}',
            '{win, plural, one{# victoire} other{# victoires}} / {lose, plural, one{une défaite} other{# défaites}}',
            'fr',
        );
        $this->assertSame([IcuMessageChecker::MISSING_ARGUMENT], array_column($result, 'type'));
        $this->assertStringContainsString('{lose}', $result[0]['detail']);
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

    public function testExtraArgument(): void
    {
        // "n" is not in the source, but the caller passes it
        $source = '{nFormatted} remaining';
        $translation = '{n, plural, one{{nFormatted} restant} other{{nFormatted} restants}}';
        $this->assertSame([], IcuMessageChecker::check($source, $translation, 'fr', ['n']));
        $this->assertSame([], IcuMessageChecker::check($source, 'Noch {nFormatted}', 'de', ['n']));
        $this->assertSame(
            [IcuMessageChecker::UNRESOLVED_PLACEHOLDER, IcuMessageChecker::MISSING_ARGUMENT],
            array_column(IcuMessageChecker::check($source, $translation, 'fr'), 'type'),
        );
    }

    public function testUnknownArgumentWithExtraArgument(): void
    {
        $this->assertSame(
            [IcuMessageChecker::UNRESOLVED_PLACEHOLDER, IcuMessageChecker::MISSING_ARGUMENT],
            array_column(
                IcuMessageChecker::check(
                    '{nFormatted} remaining',
                    '{nn, plural, one{{nFormatted} restant} other{{nFormatted} restants}}',
                    'fr',
                    ['n'],
                ),
                'type',
            ),
        );
    }

    public function testIgnoredArguments(): void
    {
        $this->assertSame([], IcuMessageChecker::check('{boy}Apprentice', 'Praktikant', 'de'));
        $this->assertSame([], IcuMessageChecker::check('Mode{translate_hint_stats}', 'Modalwert', 'de'));
    }
}
