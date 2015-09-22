<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Curl\Curl;
use Yii;
use app\components\json\OfficialJson;
use app\models\Fest;
use app\models\Mvp;
use app\models\OfficialData;
use app\models\OfficialWinData;
use yii\console\Controller;

class OfficialDataController extends Controller
{
    public function actionTest()
    {
        $obj = new OfficialJson(
            Fest::findOne(['id' => 2]),
            file_get_contents(__DIR__ . '/../data/old-results/2/2015-07-03T17-18-06.json')
        );
        
        if ($obj->sha256sum !== 'XNLnndL5YOVClyYIqqoKisBbty82S+74mtMFYlr6lbg=') {
            echo "SHA256SUM mismatch\n";
            echo "  Expect: XNLnndL5YOVClyYIqqoKisBbty82S+74mtMFYlr6lbg=\n";
            echo "  Actial: " . $obj->sha256sum . "\n";
            exit(1);
        }

        $ret = $obj->getWinCounts();
        foreach (['alpha' => 38, 'bravo' => 57] as $color => $expect) {
            if ($ret->$color !== $expect) {
                echo "WinCount mismatch ($color)\n";
                echo "  Expect: " . $expect . "\n";
                echo "  Actual: " . $ret->$color . "\n";
                exit(1);
            }
        }

        $expect = [
            [ '赤いきつね', 'こうないえん', 'alpha' ],
            [ '緑のたぬき', 'フレアだんい', 'bravo' ],
            [ '緑のたぬき', 'ヨッシー', 'bravo' ],
            [ '緑のたぬき', 'ひびき', 'bravo' ],
        ];
        foreach ($obj->getMvpList() as $i => $row) {
            if ($i >= count($expect)) {
                break;
            }
            foreach (['win_team_name', 'win_team_mvp', 'x_win_team_side'] as $j => $column) {
                if ($expect[$i][$j] !== $row[$column]) {
                    echo "MVPList mismatch:\n";
                    echo "Expect:\n";
                    var_dump($expect[$i]);
                    echo "Actual:\n";
                    var_dump($row);
                    exit(1);
                }
            }
        }

        echo "OK\n";
    }

    public function actionUpdate()
    {
        $debug = true;

        $now = $debug ? strtotime('2015-07-03 17:18:06+9') : time();
        $fest = $this->getCurrentFest($now);
        if (!$fest) {
            echo "fest closed.\n";
            return 0;
        }

        $json = $debug
            ? file_get_contents(__DIR__ . '/../data/old-results/2/2015-07-03T17-18-06.json')
            : $this->fetchJsonFromNintendo();
        if (!$json || substr($json, 0, 2) === '[]' || substr($json, 0, 2) === '{}') {
            echo "failed or empty json.\n";
            return 1;
        }

        $jsonObj = new OfficialJson($fest, $json);
        if (!$debug && $this->isDuplicated($fest, $jsonObj->sha256sum)) {
            echo "duplicated.\n";
            return 0;
        }

        $this->saveJson($fest, $json, $now);

        $winCounts = $jsonObj->getWinCounts();
        if ($winCounts->alpha < 1 && $winCounts->bravo < 1) {
            echo "winCounts error\n";
            return 1;
        }

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $modelOfficialData = new OfficialData();
            $modelOfficialData->fest_id = $fest->id;
            $modelOfficialData->sha256sum = $jsonObj->sha256sum;
            $modelOfficialData->downloaded_at = $now;
            if (!$modelOfficialData->save()) {
                echo "official_data save failed\n";
                throw new \Exception();
            }

            $modelWinData = new OfficialWinData();
            $modelWinData->data_id = $modelOfficialData->id;
            $modelWinData->color_id = 1;
            $modelWinData->count = $winCounts->alpha;
            if (!$modelWinData->save()) {
                echo "official_win_data save failed (alpha)\n";
                throw new \Exception();
            }

            $modelWinData = new OfficialWinData();
            $modelWinData->data_id = $modelOfficialData->id;
            $modelWinData->color_id = 2;
            $modelWinData->count = $winCounts->bravo;
            if (!$modelWinData->save()) {
                echo "official_win_data save failed (bravo)\n";
                throw new \Exception();
            }

            foreach ($jsonObj->getMvpList() as $mvpInfo) {
                $mvp = new Mvp();
                $mvp->data_id = $modelOfficialData->id;
                $mvp->color_id = $mvpInfo['x_win_team_side'] === 'alpha' ? 1 : 2;
                $mvp->name = $mvpInfo['win_team_mvp'];
                if (!$mvp->save()) {
                    echo "MVP save failed\n";
                    throw new \Exception();
                }
            }

            $transaction->commit();
            echo "OK\n";
            return 0;
        } catch (\Exception $e) {
        }
        $transaction->rollback();
        return 2;
    }

    private function getCurrentFest($now)
    {
        return Fest::find()
            ->andWhere(['<=', 'fest.start_at', $now])
            ->andWhere(['>', 'fest.end_at', $now])
            ->one();
    }

    private function fetchJsonFromNintendo()
    {
        $url = 'http://s3-ap-northeast-1.amazonaws.com/splatoon-data.nintendo.net/recent_results.json';
        $curl = new Curl();
        $curl->setUserAgent('fest.ink (+https://fest.ink/)');
        $ret = $curl->get($url, ['_' => time()]);
        return $curl->error ? false : (string)$ret;
    }

    private function isDuplicated(Fest $fest, $sha256sum)
    {
        return !!OfficialData::findOne([
            'fest_id' => $fest->id,
            'sha256sum' => $sha256sum,
        ]);
    }

    private function saveJson(Fest $fest, $json, $fetchedAt)
    {
        $filepath = __DIR__ . '/../runtime/official-data/' .
            'fest-' . $fest->id . '/' . date('Y-m-d\TH-i-s', $fetchedAt) . '.json';

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        file_put_contents($filepath, $json);
    }
}
