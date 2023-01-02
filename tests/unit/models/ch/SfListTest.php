<?php

declare(strict_types=1);

namespace tests\models\ch;

use Codeception\Test\Unit;
use app\models\ch\SfList;

class SfListTest extends Unit
{
    public function testIntegerList()
    {
        $model = SfList::create('3, 2, 1'); // will be sorted
        $this->assertInstanceOf(SfList::class, $model);
        $this->assertCount(3, $model->items);
        $this->assertEquals(1, $model->items[0]->value);
        $this->assertEquals(2, $model->items[1]->value);
        $this->assertEquals(3, $model->items[2]->value);
        $this->assertEquals('1,2,3', (string)$model);
    }

    public function testStringList()
    {
        $model = SfList::create(
            '"\\\\Not\\"A;Brand";v="99", "Chromium";v="84", "Google Chrome";v="84"',
        );
        $this->assertInstanceOf(SfList::class, $model);
        $this->assertCount(3, $model->items);

        $this->assertEquals('"Chromium";v="84"', (string)$model->items[0]);
        $this->assertEquals('"Google Chrome";v="84"', (string)$model->items[1]);
        $this->assertEquals('"\\\\Not\"A;Brand";v="99"', (string)$model->items[2]);
        $this->assertEquals(
            '"Chromium";v="84","Google Chrome";v="84","\\\\Not\\"A;Brand";v="99"',
            (string)$model,
        );
    }
}
