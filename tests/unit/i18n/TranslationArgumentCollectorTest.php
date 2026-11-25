<?php

declare(strict_types=1);

namespace tests\i18n;

use Codeception\Test\Unit;
use app\components\i18n\TranslationArgumentCollector;

class TranslationArgumentCollectorTest extends Unit
{
    public function testCollectsArgumentsPerMessage(): void
    {
        $this->assertSame(
            [
                'app' => [
                    'Hello' => ['name'],
                    'Bye' => [],
                ],
                'app-show' => [
                    'Hello' => ['n', 'nFormatted'],
                ],
            ],
            TranslationArgumentCollector::collect([
                ['category' => 'app', 'message' => 'Hello', 'arguments' => ['name']],
                ['category' => 'app', 'message' => 'Bye', 'arguments' => []],
                ['category' => 'app-show', 'message' => 'Hello', 'arguments' => ['n', 'nFormatted']],
            ]),
        );
    }

    public function testTakesArgumentsPassedByAllCalls(): void
    {
        $this->assertSame(
            ['app' => ['Hello' => ['n']]],
            TranslationArgumentCollector::collect([
                ['category' => 'app', 'message' => 'Hello', 'arguments' => ['n', 'nFormatted']],
                ['category' => 'app', 'message' => 'Hello', 'arguments' => ['x', 'n']],
            ]),
        );
    }

    public function testUnknownArgumentsMeanNone(): void
    {
        $this->assertSame(
            ['app' => ['Hello' => []]],
            TranslationArgumentCollector::collect([
                ['category' => 'app', 'message' => 'Hello', 'arguments' => ['n']],
                ['category' => 'app', 'message' => 'Hello', 'arguments' => null],
                ['category' => 'app', 'message' => 'Hello', 'arguments' => ['n']],
            ]),
        );
    }

    public function testCategoryWithDynamicMessagesHasNoArguments(): void
    {
        // e.g., Yii::t('app', $message, ['x' => 1]) might also call "Hello"
        $this->assertSame(
            [
                'app' => ['Hello' => []],
                'app-show' => ['Hello' => ['n']],
            ],
            TranslationArgumentCollector::collect(
                [
                    ['category' => 'app', 'message' => 'Hello', 'arguments' => ['n']],
                    ['category' => 'app-show', 'message' => 'Hello', 'arguments' => ['n']],
                ],
                [
                    ['line' => 1, 'category' => 'app'],
                    ['line' => 2, 'category' => null],
                ],
            ),
        );
    }
}
