<?php

declare(strict_types=1);

namespace tests\models\ch;

use Codeception\Test\Unit;
use app\models\ch\SfItem;

use function base64_encode;
use function random_bytes;

class SfItemTest extends Unit
{
    public function testInteger()
    {
        $model = SfItem::create('12345');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(12345, $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals('12345', (string)$model);

        $model = SfItem::create('-12345');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(-12345, $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals('-12345', (string)$model);

        $model = SfItem::create('12345;b=2; a="1"');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(12345, $model->value);
        $this->assertCount(2, $model->params);
        $this->assertEquals('1', $model->params['a']);
        $this->assertEquals(2, $model->params['b']);
        $this->assertEquals('12345;a="1";b=2', (string)$model);
    }

    public function testDecimal()
    {
        $model = SfItem::create('12345.678');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(12345.678, $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals('12345.678', (string)$model);

        $model = SfItem::create('-12345.678');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(-12345.678, $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals('-12345.678', (string)$model);

        $model = SfItem::create('12345.678;z=1.234');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(12345.678, $model->value);
        $this->assertCount(1, $model->params);
        $this->assertEquals(1.234, $model->params['z']);
        $this->assertEquals('12345.678;z=1.234', (string)$model);
    }

    public function testBoolean()
    {
        $model = SfItem::create('?0');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(false, $model->value);
        $this->assertEmpty($model->params);

        $model = SfItem::create('?1');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(true, $model->value);
        $this->assertEmpty($model->params);

        $model = SfItem::create('?1; t=?1; f=?0');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals(true, $model->value);
        $this->assertCount(2, $model->params);
        $this->assertEquals(true, $model->params['t']);
        $this->assertEquals(false, $model->params['f']);
        $this->assertEquals('?1;f=?0;t=?1', (string)$model);
    }

    public function testString()
    {
        $model = SfItem::create('"abc"');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals('abc', $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals('"abc"', (string)$model);

        $model = SfItem::create('"a\\\\b\\"c"');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals('a\b"c', $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals('"a\\\\b\\"c"', (string)$model);

        $model = SfItem::create('"a\\\\b\\"c"');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals('a\b"c', $model->value);
    }

    public function testBinary()
    {
        $binary = random_bytes(32);
        $b64 = base64_encode($binary);

        $model = SfItem::create(':' . $b64 . ':');
        $this->assertInstanceOf(SfItem::class, $model);
        $this->assertEquals($binary, $model->value);
        $this->assertEmpty($model->params);
        $this->assertEquals(':' . $b64 . ':', (string)$model);
    }
}
