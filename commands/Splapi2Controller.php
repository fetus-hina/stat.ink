<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Curl\Curl;
use Exception;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Map2;
use app\models\Rule2;
use app\models\Schedule2;
use app\models\ScheduleMap2;
use app\models\ScheduleMode2;
use stdClass;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function array_map;
use function count;
use function sprintf;
use function usort;

class Splapi2Controller extends Controller
{
    public function actionUpdate(): int
    {
        $json = $this->queryJson('https://splapi2.stat.ink/schedule');
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($json as $modeKey => $json_) {
            if (!$mode = ScheduleMode2::findOne(['key' => $modeKey])) {
                $this->stderr("Unknown mode \"$modeKey\", skip...\n");
                continue;
            }
            $this->stderr('Process ' . $mode->name . "...\n");
            $this->importSchedules($mode, $json_);
        }
        $transaction->commit();
        return 0;
    }

    private function importSchedules(ScheduleMode2 $mode, array $list)
    {
        usort($list, fn ($a, $b) => $a->start->unixtime <=> $b->start->unixtime);
        foreach ($list as $schedule) {
            $this->importSchedule($mode, $schedule);
        }
    }

    private function importSchedule(ScheduleMode2 $mode, stdClass $info)
    {
        static $rules; // [ 'nawabari' => 1 ]
        if (!$rules) {
            $rules = ArrayHelper::map(Rule2::find()->asArray()->all(), 'key', 'id');
        }
        static $maps; // [ 'battera' => 1 ]
        if (!$maps) {
            $maps = ArrayHelper::map(Map2::find()->asArray()->all(), 'key', 'id');
        }
        $period = BattleHelper::calcPeriod2($info->start->unixtime);
        if (!$schedule = Schedule2::findOne(['period' => $period, 'mode_id' => $mode->id])) {
            $schedule = Yii::createObject([
                'class' => Schedule2::class,
                'period' => $period,
                'mode_id' => $mode->id,
            ]);
        }
        $schedule->rule_id = $rules[$info->mode->key];
        if ($schedule->isNewRecord || $schedule->dirtyAttributes) {
            if (!$schedule->save()) {
                $this->stderr('Schedule insert/update error at line ' . __LINE__ . "\n");
                throw new Exception();
            }
            echo 'Created or updated schedule ' . Json::encode($schedule) . "\n";
        }

        $exists = $schedule->getScheduleMaps()->count();
        if ($exists == count($info->stages)) {
            $matches = $schedule->getScheduleMaps()
                ->andWhere(['in', 'map_id', array_map(
                    fn (stdClass $stage): int => $maps[$stage->key],
                    $info->stages,
                ),
                ])
                ->count();
            if ($exists == $matches) {
                $this->stderr('Nothing changed. ' . Json::encode($schedule) . "\n");
                return;
            }
        }
        $this->stderr('Something changed (or new schedule) ' . Json::encode($schedule) . "\n");
        ScheduleMap2::deleteAll(['schedule_id' => $schedule->id]);
        foreach ($info->stages as $st) {
            $stage = Yii::createObject([
                'class' => ScheduleMap2::class,
                'schedule_id' => $schedule->id,
                'map_id' => $maps[$st->key],
            ]);
            if (!$stage->save()) {
                $this->stderr('Could not insert to schedule_map2. ' . Json::encode($stage) . "\n");
                throw new Exception();
            }
        }
        $this->stderr("  => updated\n");
    }

    private function queryJson(string $url, array $data = [])
    {
        echo "Querying {$url} ...\n";
        $curl = new Curl();
        $curl->setUserAgent(sprintf(
            '%s/%s (+%s)',
            'stat.ink',
            Yii::$app->version,
            'https://github.com/fetus-hina/stat.ink',
        ));
        $curl->get($url, $data);
        if ($curl->error) {
            throw new Exception("Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}");
        }
        return Json::decode($curl->rawResponse, false);
    }
}
