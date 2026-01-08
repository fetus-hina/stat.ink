<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Yii;
use app\components\helpers\UserTimeZone;
use yii\base\Action;
use yii\web\Response;

class GuessTimezoneAction extends Action
{
    public function run()
    {
        $resp = Yii::$app->response;
        $resp->format = Response::FORMAT_JSON;
        $resp->charset = 'UTF-8';
        if ($data = $this->makeData()) {
            $resp->data = $data;
        } else {
            $resp->statusCode = 500;
            $resp->data = [
                'error' => 'Could not guess your time zone',
            ];
        }
    }

    public function makeData(): ?array
    {
        if (!$geoip = UserTimeZone::guessByGeoIPEx()) {
            return null;
        }

        return [
            'geoip' => $geoip[1],
            'guessed' => $geoip[0]
                ? [
                    'identifier' => $geoip[0]->identifier,
                    'name' => Yii::t('app-tz', $geoip[0]->name),
                    'name_en' => $geoip[0]->name,
                ]
                : null,
        ];
    }
}
