<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
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

class Splapi2Controller extends Controller
{
    public function actionUpdate() : int
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
        usort($list, function ($a, $b) {
            return $a->start->unixtime <=> $b->start->unixtime;
        });
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
                $this->stderr("Schedule insert/update error at line " . __LINE__ . "\n");
                throw new \Exception();
            }
            echo "Created or updated schedule " . Json::encode($schedule) . "\n";
        }

        $exists = $schedule->getScheduleMaps()->count();
        if ($exists == count($info->stages)) {
            $matches = $schedule->getScheduleMaps()
                ->andWhere(['in', 'map_id', array_map(
                    function (stdClass $stage) use ($maps) : int {
                        return $maps[$stage->key];
                    },
                    $info->stages
                )])
                ->count();
            if ($exists == $matches) {
                $this->stderr("Nothing changed. " . Json::encode($schedule) . "\n");
                return;
            }
        }
        $this->stderr("Something changed (or new schedule) " . Json::encode($schedule) . "\n");
        ScheduleMap2::deleteAll(['schedule_id' => $schedule->id]);
        foreach ($info->stages as $st) {
            $stage = Yii::createObject([
                'class' => ScheduleMap2::class,
                'schedule_id' => $schedule->id,
                'map_id' => $maps[$st->key],
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

    /*
        public function actionMapUpdateAll()
        {
            $transaction = Yii::$app->db->beginTransaction();
            PeriodMap::deleteAll();
            SplatfestMap::deleteAll([
                'splatfest_id' => array_map(
                    function (Splatfest $fest) {
                        return $fest->id;
                    },
                    Splatfest::find()
                        ->innerJoinWith(['region'])
                        ->andWhere(['{{region}}.[[key]]' => 'jp'])
                        ->all()
                ),
            ]);
            $this->mapUpdateRegular();
            $this->mapUpdateGachi();
            $this->mapUpdateSplatfest();
            $transaction->commit();
        }
    
        public function actionMapUpdate()
        {
            $transaction = Yii::$app->db->beginTransaction();
            $this->mapUpdateRegular();
            $this->mapUpdateGachi();
            $this->mapUpdateSplatfest();
            $transaction->commit();
        }
    
        public function actionMapUpdateSplatfest()
        {
            $transaction = Yii::$app->db->beginTransaction();
            $this->mapUpdateSplatfest();
            $transaction->commit();
        }
    
        private function mapUpdateRegular()
        {
            echo "regular...\n";
            $latestPeriod = $this->getLatestPeriod(GameMode::findOne(['key' => 'regular']));
            $currntPeriod = \app\components\helpers\Battle::calcPeriod(time());
            $futureOnly = ($latestPeriod >= $currntPeriod);
            $json = array_filter(
                array_map(
                    function ($item) {
                        $item->period = \app\components\helpers\Battle::calcPeriod(
                            strtotime($item->start)
                        );
                        return $item;
                    },
                    $this->queryJson(
                        $futureOnly
                            ? 'https://splapi.fetus.jp/regular/next_all'
                            : 'https://splapi.fetus.jp/regular'
                    )->result
                ),
                function ($item) use ($latestPeriod) {
                    return $item->period > $latestPeriod;
                }
            );
    
            if (empty($json)) {
                echo "no data updated.\n";
                return;
            }
    
            printf("count(new_data) = %d\n", count($json));
            usort($json, function ($a, $b) {
                return $a->period - $b->period;
            });
    
            echo "Converting to insert data...\n";
            $map = $this->getMapTable();
            $rule_id = Rule::findOne(['key' => 'nawabari'])->id;
            $insert = [];
            foreach ($json as $item) {
                foreach ($item->maps as $mapName) {
                    if (isset($map[$mapName])) {
                        $insert[] = [
                            $item->period,
                            $rule_id,
                            $map[$mapName],
                        ];
                    } else {
                        echo "Unknown map name: {$mapName}\n";
                    }
                }
            }
    
            echo "inserting...\n";
            Yii::$app->db->createCommand()->batchInsert(
                PeriodMap::tableName(),
                [ 'period', 'rule_id', 'map_id' ],
                $insert
            )->execute();
            echo "done.\n";
        }
    
        private function mapUpdateGachi()
        {
            echo "gachi...\n";
            $gameMode = GameMode::findOne(['key' => 'gachi']);
            $latestPeriod = $this->getLatestPeriod($gameMode);
            $currntPeriod = \app\components\helpers\Battle::calcPeriod(time());
            $futureOnly = ($latestPeriod >= $currntPeriod);
            $json = array_filter(
                array_map(
                    function ($item) {
                        $item->period = \app\components\helpers\Battle::calcPeriod(
                            strtotime($item->start)
                        );
                        return $item;
                    },
                    $this->queryJson(
                        $futureOnly
                            ? 'https://splapi.fetus.jp/gachi/next_all'
                            : 'https://splapi.fetus.jp/gachi'
                    )->result
                ),
                function ($item) use ($latestPeriod) {
                    return $item->period > $latestPeriod;
                }
            );
    
            if (empty($json)) {
                echo "no data updated.\n";
                return;
            }
    
            printf("count(new_data) = %d\n", count($json));
            usort($json, function ($a, $b) {
                return $a->period - $b->period;
            });
    
            echo "Converting to insert data...\n";
            $map = $this->getMapTable();
            $rule = $this->getRuleTable($gameMode);
            $insert = [];
            foreach ($json as $item) {
                if (!isset($rule[$item->rule])) {
                    echo "Unknown rule name: {$item->rule}\n";
                    continue;
                }
                foreach ($item->maps as $mapName) {
                    if (isset($map[$mapName])) {
                        $insert[] = [
                            $item->period,
                            $rule[$item->rule],
                            $map[$mapName],
                        ];
                    } else {
                        echo "Unknown map name: {$mapName}\n";
                    }
                }
            }
    
            echo "inserting...\n";
            Yii::$app->db->createCommand()->batchInsert(
                PeriodMap::tableName(),
                [ 'period', 'rule_id', 'map_id' ],
                $insert
            )->execute();
            echo "done.\n";
        }
    
        private function getLatestPeriod(GameMode $gameMode)
        {
            $o = PeriodMap::find()
                ->andWhere([
                    'in',
                    'rule_id',
                    array_map(
                        function ($a) {
                            return $a->id;
                        },
                        $gameMode->rules
                    )
                ])
                ->orderBy('{{period_map}}.[[period]] DESC')
                ->limit(1)
                ->one();
            return $o ? $o->period : 0;
        }
    
        private function mapUpdateSplatfest()
        {
            if (!$this->needUpdateSplatfest()) {
                return;
            }
    
            echo "splatfest...\n";
    
            $json = $this->queryJson('https://splapi.fetus.jp/fes');
            foreach ($json->result as $data) {
                $start_at = strtotime($data->start);
                $end_at = strtotime($data->end);
                $t = gmdate('Y-m-d\TH:i:sP', (int)(($start_at + $end_at) / 2));
                $fest = Splatfest::find()
                    ->innerJoinWith('region', false)
                    ->andWhere(['and',
                        ['{{region}}.[[key]]' => 'jp'],
                        ['<=', '{{splatfest}}.[[start_at]]', $t],
                        ['>',  '{{splatfest}}.[[end_at]]', $t],
                    ])
                    ->one();
                if (!$fest) {
                    continue;
                }
                if ($fest->getSplatfestMaps()->count() > 0) {
                    continue;
                }
                echo "new data for [" . $fest->name . "]\n";
                if (!$maps = SplapiMap::findAll(['name' => $data->maps])) {
                    echo "  no map data available...\n";
                    continue;
                }
                foreach ($maps as $map) {
                    $o = new SplatfestMap();
                    $o->attributes = [
                        'splatfest_id' => $fest->id,
                        'map_id' => $map->map_id,
                    ];
                    if (!$o->save()) {
                        throw new \Exception('Save failed');
                    }
                }
            }
        }
    
        private function needUpdateSplatfest()
        {
            // データが何もなければ取得が必    要
            $count = SplatfestMap::find()
                ->innerJoinWith(['splatfest', 'splatfest.region'])
                ->andWhere(['{{region}}.[[key]]' => 'jp'])
                ->count();
            if ($count < 1) {
                return true;
            }
    
            // 今がフェス中でなければ不要
            $now = gmdate(
                'Y-m-d\TH:i:sP',
                (int)(@$_SERVER['REQUEST_TIME'] ?: time())
            );
            $fest = Splatfest::find()
                ->innerJoinWith('region', false)
                ->andWhere(['and',
                    ['{{region}}.[[key]]' => 'jp'],
                    ['<=', '{{splatfest}}.[[start_at]]', $now],
                    ['>',  '{{splatfest}}.[[end_at]]', $now],
                ])
                ->one();
            if (!$fest) {
                return false;
            }
    
            // マップ情    報をもっていれば不要
            $count = SplatfestMap::find()
                ->andWhere(['{{splatfest_map}}.[[splatfest_id]]' => $fest->id])
                ->count();
            return $count < 1;
        }
    
    
        private function getMapTable()
        {
            $ret = [];
            foreach (SplapiMap::find()->all() as $a) {
                $ret[$a->name] = $a->map_id;
            }
            return $ret;
        }
    
        private function getRuleTable(GameMode $gameMode)
        {
            $ret = [];
            foreach (SplapiRule::find()->all() as $a) {
                $ret[$a->name] = $a->rule_id;
            }
            return $ret;
        }
    */
}
