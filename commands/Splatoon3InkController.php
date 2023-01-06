<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Exception;
use Yii;
use app\commands\splatoon3Ink\UpdateSalmonSchedule;
use app\commands\splatoon3Ink\UpdateSchedule;
use app\components\helpers\splatoon3ink\ScheduleParser;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;

use function compact;
use function hash_hmac;
use function vsprintf;

final class Splatoon3InkController extends Controller
{
    use UpdateSalmonSchedule;
    use UpdateSchedule;

    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        $schedules = ScheduleParser::parseAll(
            $this->queryJson('https://splatoon3.ink/data/schedules.json'),
        );

        $status = 0;
        $status |= $this->updateSchedule($schedules);
        $status |= $this->updateSalmonSchedule($schedules);
        return $status === 0 ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    public function actionUpdateSchedule(): int
    {
        return $this->updateSchedule(
            ScheduleParser::parseAll(
                $this->queryJson('https://splatoon3.ink/data/schedules.json'),
            ),
        );
    }

    public function actionUpdateSalmonSchedule(): int
    {
        return $this->updateSalmonSchedule(
            ScheduleParser::parseAll(
                $this->queryJson('https://splatoon3.ink/data/schedules.json'),
            ),
        );
    }

    private function queryJson(string $url, array $data = []): array
    {
        return Yii::$app->cache->getOrSet(
            hash_hmac('sha256', Json::encode(compact('url', 'data')), __METHOD__),
            function () use ($url, $data): array {
                echo "Querying {$url} ...\n";
                $curl = new Curl();
                $curl->setUserAgent(
                    vsprintf('%s/%s (+%s)', [
                        'stat.ink',
                        Yii::$app->version,
                        'https://github.com/fetus-hina/stat.ink',
                    ]),
                );
                $curl->get($url, $data);
                if ($curl->error) {
                    throw new Exception(
                        "Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}",
                    );
                }
                return Json::decode($curl->rawResponse, true);
            },
            duration: 1800, // 30 min
        );
    }
}
