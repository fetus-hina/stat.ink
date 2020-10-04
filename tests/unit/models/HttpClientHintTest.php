<?php

declare(strict_types=1);

namespace tests\models\ch;

use Codeception\Test\Unit;
use Yii;
use app\models\HttpClientHint;
use app\models\ch\SfItem;
use app\models\ch\SfList;
use yii\web\HeaderCollection;

class HttpClientHintTest extends Unit
{
    public function testCreateFromHeaders()
    {
        $headers = Yii::createObject(HeaderCollection::class);
        $headers->set('Sec-CH-UA', '"\\\\Not\\"A;Brand";v="99", "Chromium";v="84", "Google Chrome";v="84"'); //phpcs:disable
        $headers->set('Sec-CH-UA-Mobile', '?0');

        $data = HttpClientHint::createDataFromHeaders($headers);
        $this->assertNotNull($data);
        $this->assertInstanceof(SfList::class, $data['sec-ch-ua']);
        $this->assertInstanceof(SfItem::class, $data['sec-ch-ua-mobile']);
    }

    public function testCreateFromEmptyHeaders()
    {
        $headers = Yii::createObject(HeaderCollection::class);

        $data = HttpClientHint::createDataFromHeaders($headers);
        $this->assertNull($data);
    }
}
