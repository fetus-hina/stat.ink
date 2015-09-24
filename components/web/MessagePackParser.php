<?php
namespace app\components\web;

use yii\web\RequestParserInterface;
use yii\web\BadRequestHttpException;

class MessagePackParser implements RequestParserInterface
{
    public $throwException = true;

    public function parse($rawBody, $contentType)
    {
        if (!extension_loaded('msgpack')) {
            throw new BadRequestHttpException('Msgpack is not supported in this app server');
        }
        $unpacked = @msgpack_unpack($rawBody);
        if ($unpacked === null && $rawBody !== chr(0xc0)) {
            throw new BadRequestHttpException('Invalid MsgPack data in request body');
        }
        return $unpacked;
    }
}
