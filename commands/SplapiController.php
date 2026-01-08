<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Curl\Curl;
use Exception;
use Yii;
use app\components\helpers\Battle;
use app\models\GameMode;
use app\models\PeriodMap;
use app\models\Rule;
use app\models\SplapiMap;
use app\models\SplapiRule;
use app\models\Splatfest;
use app\models\SplatfestMap;
use yii\console\Controller;
use yii\helpers\Json;

use function array_filter;
use function array_map;
use function count;
use function gmdate;
use function printf;
use function sprintf;
use function strtotime;
use function time;
use function usort;

class SplapiController extends Controller
{
    public function init()
    {
        Yii::$app->timeZone = 'Asia/Tokyo';
    }

    public function actionMapUpdateAll()
    {
        $transaction = Yii::$app->db->beginTransaction();
        PeriodMap::deleteAll();
        SplatfestMap::deleteAll([
            'splatfest_id' => array_map(
                fn (Splatfest $fest) => $fest->id,
                Splatfest::find()
                    ->innerJoinWith(['region'])
                    ->andWhere(['{{region}}.[[key]]' => 'jp'])
                    ->all(),
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
        $currntPeriod = Battle::calcPeriod(time());
        $futureOnly = ($latestPeriod >= $currntPeriod);
        $json = array_filter(
            array_map(
                function ($item) {
                    $item->period = Battle::calcPeriod(
                        strtotime($item->start),
                    );
                    return $item;
                },
                $this->queryJson(
                    $futureOnly
                        ? 'https://splapi.fetus.jp/regular/next_all'
                        : 'https://splapi.fetus.jp/regular',
                )->result,
            ),
            fn ($item) => $item->period > $latestPeriod,
        );

        if (!$json) {
            echo "no data updated.\n";
            return;
        }

        printf("count(new_data) = %d\n", count($json));
        usort($json, fn ($a, $b) => $a->period - $b->period);

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
            ['period', 'rule_id', 'map_id'],
            $insert,
        )->execute();
        echo "done.\n";
    }

    private function mapUpdateGachi()
    {
        echo "gachi...\n";
        $gameMode = GameMode::findOne(['key' => 'gachi']);
        $latestPeriod = $this->getLatestPeriod($gameMode);
        $currntPeriod = Battle::calcPeriod(time());
        $futureOnly = ($latestPeriod >= $currntPeriod);
        $json = array_filter(
            array_map(
                function ($item) {
                    $item->period = Battle::calcPeriod(
                        strtotime($item->start),
                    );
                    return $item;
                },
                $this->queryJson(
                    $futureOnly
                        ? 'https://splapi.fetus.jp/gachi/next_all'
                        : 'https://splapi.fetus.jp/gachi',
                )->result,
            ),
            fn ($item) => $item->period > $latestPeriod,
        );

        if (!$json) {
            echo "no data updated.\n";
            return;
        }

        printf("count(new_data) = %d\n", count($json));
        usort($json, fn ($a, $b) => $a->period - $b->period);

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
            ['period', 'rule_id', 'map_id'],
            $insert,
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
                    fn ($a) => $a->id,
                    $gameMode->rules,
                ),
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
                    ['>', '{{splatfest}}.[[end_at]]', $t],
                ])
                ->one();
            if (!$fest) {
                continue;
            }
            if ($fest->getSplatfestMaps()->count() > 0) {
                continue;
            }
            echo 'new data for [' . $fest->name . "]\n";
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
                    throw new Exception('Save failed');
                }
            }
        }
    }

    private function needUpdateSplatfest()
    {
        // データが何もなければ取得が必要
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
            (int)(@$_SERVER['REQUEST_TIME'] ?: time()),
        );
        $fest = Splatfest::find()
            ->innerJoinWith('region', false)
            ->andWhere(['and',
                ['{{region}}.[[key]]' => 'jp'],
                ['<=', '{{splatfest}}.[[start_at]]', $now],
                ['>', '{{splatfest}}.[[end_at]]', $now],
            ])
            ->one();
        if (!$fest) {
            return false;
        }

        // マップ情報をもっていれば不要
        $count = SplatfestMap::find()
            ->andWhere(['{{splatfest_map}}.[[splatfest_id]]' => $fest->id])
            ->count();
        return $count < 1;
    }

    private function queryJson($url, $data = [])
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
}
