<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Curl\Curl;
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
use yii\helpers\Console;
use yii\helpers\Json;

class Splatoon2InkController extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate() : int
    {
        $status = 0;
        $status |= $this->actionUpdateSchedule();
        // $status |= $this->actionUpdateCoopSchedule();
        return $status === 0 ? 0 : 1;
    }

    public function actionUpdateSchedule() : int 
    {
        $json = $this->queryJson('https://splatoon2.ink/data/schedules.json');

        $transaction = Yii::$app->db->beginTransaction();
        try {
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
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            $transaction->rollBack();
            return 1;
        }
    }

    private function importSchedules(ScheduleMode2 $mode, array $list)
    {
        usort($list, function (stdClass $a, stdClass $b) : int {
            return $a->start_time <=> $b->start_time;
        });
        foreach ($list as $schedule) {
            $this->importSchedule($mode, $schedule);
        }
    }

    private function importSchedule(ScheduleMode2 $mode, stdClass $info)
    {
        static $rules;
        if (!$rules) {
            $tmpRules = ArrayHelper::map(
                Rule2::find()->asArray()->all(),
                'key',
                'id'
            );
            $rules = [
                'turf_war'      => $tmpRules['nawabari'],
                'splat_zones'   => $tmpRules['area'],
                'tower_control' => $tmpRules['yagura'],
                'rainmaker'     => $tmpRules['hoko'],
                'clam_blitz'    => $tmpRules['asari'],
            ];
        }

        static $maps; // [ 'battera' => 1 ]
        if (!$maps) {
            $maps = ArrayHelper::map(
                Map2::find()
                    ->andWhere(['not', ['splatnet' => null]])
                    ->asArray()
                    ->all(),
                'splatnet',
                'id'
            );
        }

        $period = BattleHelper::calcPeriod2($info->start_time);
        if (!$schedule = Schedule2::findOne(['period' => $period, 'mode_id' => $mode->id])) {
            $schedule = Yii::createObject([
                'class' => Schedule2::class,
                'period' => $period,
                'mode_id' => $mode->id,
            ]);
        }
        $schedule->rule_id = $rules[$info->rule->key];
        if ($schedule->isNewRecord || $schedule->dirtyAttributes) {
            if (!$schedule->save()) {
                $this->stderr("Schedule insert/update error at line " . __LINE__ . "\n");
                throw new \Exception();
            }
            echo "Created or updated schedule " . Json::encode($schedule) . "\n";
        }

        $exists = $schedule->getScheduleMaps()->count();
        $stages = array_filter([
            $info->stage_a ?? null,
            $info->stage_b ?? null,
            $info->stage_c ?? null,
        ]);

        if ($exists == count($stages)) {
            $matches = $schedule->getScheduleMaps()
                ->andWhere(['in', 'map_id', array_map(
                    function (stdClass $stage) use ($maps) : int {
                        return $maps[$stage->id] ?? -1;
                    },
                    $stages
                )])
                ->count();
            if ($exists == $matches) {
                $this->stderr("Nothing changed. " . Json::encode($schedule) . "\n");
                return;
            }
        }

        $this->stderr("Something changed (or new schedule) " . Json::encode($schedule) . "\n");
        ScheduleMap2::deleteAll(['schedule_id' => $schedule->id]);
        foreach ($stages as $st) {
            if (!isset($maps[$st->id])) {
                continue;
            }

            $stage = Yii::createObject([
                'class' => ScheduleMap2::class,
                'schedule_id' => $schedule->id,
                'map_id' => $maps[$st->id],
            ]);

            if (!$stage->save()) {
                $this->stderr('Could not insert to schedule_map2. ' . Json::encode($stage) . "\n");
                throw new \Exception();
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
            'https://github.com/fetus-hina/stat.ink'
        ));
        $curl->get($url, $data);
        if ($curl->error) {
            throw new \Exception("Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}");
        }
        return Json::decode($curl->rawResponse, false);
    }
}
