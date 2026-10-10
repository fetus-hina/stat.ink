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
            [['category' => 'app', 'message' => 'Hello', 'line' => 2]],
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
                ['category' => 'app-email', 'message' => '{n} items', 'line' => 2],
                ['category' => 'app', 'message' => 'Trailing comma', 'line' => 3],
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
            [['category' => 'app', 'message' => 'Multi-line', 'line' => 3]],
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
            [['category' => 'app-apidoc2', 'message' => 'Long messages are split into lines.', 'line' => 2]],
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
        $this->assertSame([2, 3, 4, 5, 6, 9], $result['dynamic']);
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
                ['category' => 'app-slack', 'message' => 'won', 'line' => 2],
                ['category' => 'app', 'message' => 'Mode', 'line' => 3],
                ['category' => 'app', 'message' => 'Nullsafe', 'line' => 4],
                ['category' => 'app-ability2', 'message' => 'Ink Saver', 'line' => 5],
                ['category' => 'app', 'message' => 'Qualified', 'line' => 6],
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
        $this->assertSame([2, 3], $result['dynamic']);
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

    public function testExtractsCallsInsideViewTemplate(): void
    {
        $result = TranslationCallExtractor::extract(<<<'PHP'
            <h1><?= Html::encode(Yii::t('app', 'Title')) ?></h1>
            <p><?= Yii::t('app', 'Body') ?></p>
            PHP);

        $this->assertSame(
            [
                ['category' => 'app', 'message' => 'Title', 'line' => 1],
                ['category' => 'app', 'message' => 'Body', 'line' => 2],
            ],
            $result['calls'],
        );
    }
}
