<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Exception;
use Yii;
use app\commands\splatoon3Ink\EventMessages;
use app\commands\splatoon3Ink\SplatfestMessages;
use app\commands\splatoon3Ink\UpdateEventSchedule;
use app\commands\splatoon3Ink\UpdateSalmonSchedule;
use app\commands\splatoon3Ink\UpdateSchedule;
use app\commands\splatoon3Ink\UpdateSplatfestSchedule;
use app\components\helpers\splatoon3ink\ScheduleParser;
use app\models\TranslateSourceMessage;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Json;

use function vsprintf;

final class Splatoon3InkController extends Controller
{
    use EventMessages;
    use SplatfestMessages;
    use UpdateEventSchedule;
    use UpdateSalmonSchedule;
    use UpdateSchedule;
    use UpdateSplatfestSchedule;

    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        $schedules = ScheduleParser::parseAll(
            $this->queryJson('https://splatoon3.ink/data/schedules.json'),
        );

        $status = 0;
        $status |= $this->updateSchedule($schedules);
        $status |= $this->updateEventSchedule($schedules);
        $status |= $this->updateSalmonSchedule($schedules);
        $status |= $this->updateEventMessages();
        $status |= $this->updateSplatfestSchedule(
            ScheduleParser::parseFestivals(
                $this->queryJson('https://splatoon3.ink/data/festivals.json'),
            ),
        );
        $status |= $this->updateSplatfestMessages();
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

    public function actionEventSchedule(): int
    {
        return $this->updateEventSchedule(
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

    public function actionUpdateEventMessages(): int
    {
        return $this->updateEventMessages();
    }

    public function actionUpdateSplatfestSchedule(): int
    {
        return $this->updateSplatfestSchedule(
            ScheduleParser::parseFestivals(
                $this->queryJson('https://splatoon3.ink/data/festivals.json'),
            ),
        );
    }

    public function actionUpdateSplatfestMessages(): int
    {
        return $this->updateSplatfestMessages();
    }

    private function queryJson(string $url, array $data = []): array
    {
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $url, $data],
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

    private function getOrCreateSourceMessage(string $category, string $message): TranslateSourceMessage
    {
        $model = TranslateSourceMessage::find()
            ->andWhere([
                'category' => $category,
                'message' => $message,
            ])
            ->limit(1)
            ->one();
        if ($model) {
            return $model;
        }

        $model = Yii::createObject([
            'class' => TranslateSourceMessage::class,
            'category' => $category,
            'message' => $message,
        ]);
        if (!$model->save()) {
            throw new Exception('Failed to save TranslateSourceMessage for "' . $message . '"');
        }

        return $model;
    }
}
