<?php

declare(strict_types=1);

namespace tests\validators;

use Codeception\Test\Unit;
use app\components\validators\ArrayValidator;
use yii\base\DynamicModel;

class ArrayValidatorTest extends Unit
{
    public function testDefaultMessages(): void
    {
        $validator = new ArrayValidator(['rule' => ['integer']]);

        $this->assertSame('{attribute} must not contain more than {max} elements', $validator->tooBig);
        $this->assertSame('{attribute} must contain at least {min} elements', $validator->tooSmall);
    }

    public function testCustomMessagesAreKept(): void
    {
        $validator = new ArrayValidator([
            'rule' => ['integer'],
            'tooBig' => 'big',
            'tooSmall' => 'small',
        ]);

        $this->assertSame('big', $validator->tooBig);
        $this->assertSame('small', $validator->tooSmall);
    }

    public function testTooSmallReportsError(): void
    {
        $model = new DynamicModel(['values' => [1]]);
        $validator = new ArrayValidator(['rule' => ['integer'], 'min' => 2]);
        $validator->validateAttribute($model, 'values');

        $this->assertSame(
            'Values must contain at least 2 elements',
            $model->getFirstError('values'),
        );
    }

    public function testTooBigReportsError(): void
    {
        $model = new DynamicModel(['values' => [1, 2, 3]]);
        $validator = new ArrayValidator(['rule' => ['integer'], 'max' => 2]);
        $validator->validateAttribute($model, 'values');

        $this->assertSame(
            'Values must not contain more than 2 elements',
            $model->getFirstError('values'),
        );
    }
}
