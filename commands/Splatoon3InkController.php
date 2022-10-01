<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Yii;
use app\components\helpers\splatoon3ink\ScheduleParser;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;

final class Splatoon3InkController extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        $status = 0;
        $status |= $this->actionUpdateSchedule();
        return $status === 0 ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    public function actionUpdateSchedule(): int
    {
        $schedules = ScheduleParser::parseAll(
            $this->queryJson('https://splatoon3.ink/data/schedules.json')
        );
        var_dump($schedules);
        return ExitCode::OK;
    }

    private function queryJson(string $url, array $data = []): array
    {
        echo "Querying {$url} ...\n";
        $curl = new Curl();
        $curl->setUserAgent(
            \vsprintf('%s/%s (+%s)', [
                'stat.ink',
                Yii::$app->version,
                'https://github.com/fetus-hina/stat.ink',
            ])
        );
        $curl->get($url, $data);
        if ($curl->error) {
            throw new Exception("Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}");
        }
        return Json::decode($curl->rawResponse, true);
    }
}
