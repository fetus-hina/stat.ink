<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use yii\helpers\Json;
use yii\web\JsonResponseFormatter;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class PrettyJsonResponseFormatter extends JsonResponseFormatter
{
    /**
     * @inheritdoc
     */
    protected function formatJson($response)
    {
        $response->getHeaders()
            ->set('Content-Type', 'application/json; charset=UTF-8')
            ->set('Access-Control-Allow-Origin', '*');
        if ($response->data !== null) {
            $response->content = Json::encode(
                $response->data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
            );
        }
    }
}
