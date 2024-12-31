<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use yii\web\BadRequestHttpException;
use yii\web\Request as Base;
use yii\web\UnsupportedMediaTypeHttpException;

use function gzdecode;
use function is_array;
use function is_object;
use function stripos;
use function strtoupper;

class Request extends Base
{
    public function getMethod()
    {
        $method = @$_SERVER['REQUEST_METHOD'] ?: '?';
        if (strtoupper($method) === 'POST') {
            $type = $this->headers->get('Content-Type', '');
            if (
                stripos($type, 'application/json') !== false ||
                    stripos($type, 'application/x-msgpack') !== false
            ) {
                $params = $this->getBodyParams();
                if (is_object($params) && isset($params->{$this->methodParam})) {
                    return strtoupper($params->{$this->methodParam});
                } elseif (is_array($params) && isset($params[$this->methodParam])) {
                    return strtoupper($params[$this->methodParam]);
                }
            }
        }
        return parent::getMethod();
    }

    public function getRawBody()
    {
        $rawBody = parent::getRawBody();
        $contentEncoding = $this->headers->get('Content-Encoding', 'identity');
        switch ($contentEncoding) {
            case 'gzip':
                $rawBody = @gzdecode($rawBody);
                if ($rawBody !== false) {
                    return $rawBody;
                }
                throw new BadRequestHttpException('Request body(gziped) is broken.');

            case 'identity':
                return $rawBody;

            default:
                throw new UnsupportedMediaTypeHttpException('Unsupported Content-Encoding: ' . $contentEncoding);
        }
    }
}
