<?php

declare(strict_types=1);

namespace tests\i18n;

use Codeception\Test\Unit;
use app\components\i18n\TranslationCallExtractor;

class TranslationCallExtractorTest extends Unit
{
    public function testExtractsLiteralCall(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            echo Yii::t('app', 'Hello');
            PHP);

        $this->assertSame(
            [['category' => 'app', 'message' => 'Hello', 'line' => 2, 'arguments' => []]],
            $result['calls'],
        );
        $this->assertSame([], $result['dynamic']);
    }

    public function testExtractsCallWithParamsAndLanguage(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app-email', '{n} items', ['n' => 1], 'ja-JP');
            Yii::t('app', 'Trailing comma',);
            PHP);

        $this->assertSame(
            [
                ['category' => 'app-email', 'message' => '{n} items', 'line' => 2, 'arguments' => ['n']],
                ['category' => 'app', 'message' => 'Trailing comma', 'line' => 3, 'arguments' => []],
            ],
            $result['calls'],
        );
    }

    public function testExtractsMultiLineCallAtItsStartLine(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php

            $x = Yii::t(
                'app', // category
                /* message */ 'Multi-line',
            );
            PHP);

        $this->assertSame(
            [['category' => 'app', 'message' => 'Multi-line', 'line' => 3, 'arguments' => []]],
            $result['calls'],
        );
    }

    public function testExtractsFullyQualifiedClassName(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            \Yii::t('app', 'Qualified');
            PHP);

        $this->assertSame('Qualified', $result['calls'][0]['message']);
    }

    public function testDecodesStringLiterals(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app', 'It\'s a \\ backslash and \n is literal');
            Yii::t('app', "Don't \"quote\" \$x\t\x41\u{263A}");
            PHP);

        $this->assertSame(
            [
                'It\'s a \\ backslash and \n is literal',
                "Don't \"quote\" \$x\t\x41\u{263A}",
            ],
            array_column($result['calls'], 'message'),
        );
    }

    public function testExtractsConcatenatedLiterals(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t(
                'app-' . 'apidoc2',
                'Long messages are ' .
                "split into lines.",
            );
            PHP);

        $this->assertSame(
            [['category' => 'app-apidoc2', 'message' => 'Long messages are split into lines.', 'line' => 2, 'arguments' => []]],
            $result['calls'],
        );
    }

    public function testReportsNonLiteralArgumentsAsDynamic(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app', $message);
            Yii::t($category, 'Hello');
            Yii::t('app', 'Hello, ' . $name);
            Yii::t('app', "Hello, {$name}");
            Yii::t('app', <<<EOT
                heredoc
                EOT);
            Yii::t('app', 'Trailing dot' .);
            PHP);

        $this->assertSame([], $result['calls']);
        $this->assertSame(
            [
                ['line' => 2, 'category' => 'app'],
                ['line' => 3, 'category' => null],
                ['line' => 4, 'category' => 'app'],
                ['line' => 5, 'category' => 'app'],
                ['line' => 6, 'category' => 'app'],
                ['line' => 9, 'category' => 'app'],
            ],
            $result['dynamic'],
        );
    }

    public function testExtractsOtherTranslationApis(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            $i18n->translate('app-slack', 'won', [], $lang);
            Yii::$app->i18n->translate('app', 'Mode', [], $lang);
            $i18n?->translate('app', 'Nullsafe', [], $lang);
            Translator::translateToAll('app-ability2', 'Ink Saver');
            \app\components\helpers\Translator::translateToAll('app', 'Qualified');
            PHP);

        $this->assertSame(
            [
                ['category' => 'app-slack', 'message' => 'won', 'line' => 2, 'arguments' => []],
                ['category' => 'app', 'message' => 'Mode', 'line' => 3, 'arguments' => []],
                ['category' => 'app', 'message' => 'Nullsafe', 'line' => 4, 'arguments' => []],
                ['category' => 'app-ability2', 'message' => 'Ink Saver', 'line' => 5, 'arguments' => []],
                ['category' => 'app', 'message' => 'Qualified', 'line' => 6, 'arguments' => []],
            ],
            $result['calls'],
        );
    }

    public function testReportsNonLiteralArgumentsOfOtherTranslationApisAsDynamic(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            $i18n->translate('app', $battle->result->name, [], $lang);
            Translator::translateToAll('app', $this->name);
            PHP);

        $this->assertSame([], $result['calls']);
        $this->assertSame(
            [
                ['line' => 2, 'category' => 'app'],
                ['line' => 3, 'category' => 'app'],
            ],
            $result['dynamic'],
        );
    }

    public function testIgnoresOtherCalls(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Foo::t('app', 'Foo');
            $obj->t('app', 'Method');
            Yii::translate('app', 'Other method');
            t('app', 'Function');
            // Yii::t('app', 'Comment');
            echo 'Yii::t("app", "String")';
            PHP);

        $this->assertSame([], $result['calls']);
        $this->assertSame([], $result['dynamic']);
    }

    public function testExtractsArgumentNames(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app', 'Keyed', ['n' => $n, "nFormatted" => $fmt->asInteger($n, 0)]);
            Yii::t('app', 'Positional', [20, $x]);
            Yii::t('app', 'Empty', []);
            Yii::t('app', 'Old syntax', array('n' => 1));
            Yii::t('app', 'Trailing commas', ['n' => 1,],);
            Yii::t('app', 'Nested', [
                'a' => [1, 2],
                'b' => fn ($x) => $x,
                'c' => match (true) { $x => 1, default => 2 },
                'd' => $x ? 'y' : 'z',
            ]);
            $i18n->translate('app', 'Other API', ['x' => 1], $lang);
            PHP);

        $this->assertSame(
            [
                ['n', 'nFormatted'],
                ['0', '1'],
                [],
                ['n'],
                ['n'],
                ['a', 'b', 'c', 'd'],
                ['x'],
            ],
            array_column($result['calls'], 'arguments'),
        );
    }

    public function testReportsUnknownArgumentsAsNull(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app', 'Variable', $params);
            Yii::t('app', 'Function', compact('n'));
            Yii::t('app', 'Spread', ['n' => 1, ...$params]);
            Yii::t('app', 'Variable key', [$key => 1]);
            Yii::t('app', 'Concatenated key', ['a' . 'b' => 1]);
            PHP);

        $this->assertSame(
            [null, null, null, null, null],
            array_column($result['calls'], 'arguments'),
        );
        $this->assertSame([], $result['dynamic']);
    }

    public function testIgnoresCommasInInterpolatedStrings(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app', 'A', ['n' => "$a,$b"]);
            Yii::t('app', 'B', ['n' => "{$a['x']},{$b}", 'm' => "${c},d"]);
            PHP);

        $this->assertSame(
            [['n'], ['n', 'm']],
            array_column($result['calls'], 'arguments'),
        );
    }

    public function testNumbersPositionalArgumentsLikePhp(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <?php
            Yii::t('app', 'Numeric string key', ['5' => 1, 2]);
            Yii::t('app', 'Non-canonical key', ['05' => 1, 2]);
            Yii::t('app', 'Digit separator', [1_000 => 1, 2]);
            Yii::t('app', 'Hexadecimal', [0x10 => 1]);
            PHP);

        $this->assertSame(
            [
                ['5', '6'],
                ['05', '0'],
                ['1000', '1001'],
                ['16'],
            ],
            array_column($result['calls'], 'arguments'),
        );
    }

    public function testExtractsCallsInsideViewTemplate(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <h1><?= Html::encode(Yii::t('app', 'Title')) ?></h1>
            <p><?= Yii::t('app', 'Body') ?></p>
            PHP);

        $this->assertSame(
            [
                ['category' => 'app', 'message' => 'Title', 'line' => 1, 'arguments' => []],
                ['category' => 'app', 'message' => 'Body', 'line' => 2, 'arguments' => []],
            ],
            $result['calls'],
        );
    }
}
