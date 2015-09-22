<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use DateTime;
use DateTimeZone;
use Yii;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\components\helpers\DateTimeFormatter;
use app\models\Fest;
use app\models\Mvp;
use app\models\OfficialData;
use app\models\OfficialResult;
use app\models\Timezone;

class ViewJsonAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $id = $request->get('id');
        if (!is_scalar($id) ||
                !($fest = Fest::findOne(['id' => $id]))) {
            throw new NotFoundHttpException();
        }
        $tz = $request->get('tz');
        $tz = new DateTimeZone(
            (!is_scalar($tz) || !Timezone::findOne(['zone' => $tz]))
                ? Yii::$app->timeZone
                : $tz
        );

        $withMvp = $request->get('mvp');
        if (is_scalar($withMvp) &&
                in_array(strtolower((string)$withMvp), ['1', 't', 'true', 'y', 'yes'], true)
        ) {
            $withMvp = true;
        } else {
            $withMvp = false;
        }
        $callback = $request->get('callback');
        if (!is_scalar($callback) || !preg_match('/^[A-Za-z0-9_.]+$/', $callback)) {
            $callback = null;
        }

        $now = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        $officialResult = null;
        if ((int)$fest->start_at > $now) {
            $state = 'scheduled';
        } elseif ((int)$fest->end_at > $now) {
            $state = 'in session';
        } else {
            $state = 'closed';
            if ($fest->officialResult) {
                $officialResult = [
                    'vote' => [
                        'alpha' => (int)$fest->officialResult->alpha_people,
                        'bravo' => (int)$fest->officialResult->bravo_people,
                        'multiply' => 1,
                    ],
                    'win' => [
                        'alpha' => (int)$fest->officialResult->alpha_win,
                        'bravo' => (int)$fest->officialResult->bravo_win,
                        'multiply' => (int)$fest->officialResult->win_rate_times,
                    ],
                ];
            }
        }
        $alpha = $fest->alphaTeam;
        $bravo = $fest->bravoTeam;
        $data = array_merge(
            [
                'now'   => $now,
                'now_s' => DateTimeFormatter::unixTimeToString($now, $tz),
                'wins'  => array_map(
                    function (OfficialData $data) use ($withMvp, $tz) {
                        $alpha = $data->alpha;
                        $bravo = $data->bravo;
                        if ($withMvp) {
                            list($alphaMvpList, $bravoMvpList) = $this->fetchMvpList($data);
                        } else {
                            $alphaMvpList = $bravoMvpList = null;
                        }
                        return [
                            'at'    => $data->downloaded_at,
                            'at_s'  => DateTimeFormatter::unixTimeToString($data->downloaded_at, $tz),
                            'alpha' => $alpha ? $alpha->count : 0,
                            'bravo' => $bravo ? $bravo->count : 0,
                            'alphaMvp' => $alpha ? $alphaMvpList : null,
                            'bravoMvp' => $bravo ? $bravoMvpList : null,
                        ];
                    },
                    $fest->officialDatas
                ),
            ],
            $fest->toJsonArray($tz)
        );
        if ($callback !== null) {
            Yii::$app->getResponse()->format = 'jsonp';
            return [
                'data' => $data,
                'callback' => $callback,
            ];
        } else {
            Yii::$app->getResponse()->format = 'json';
            return $data;
        }
    }

    private function fetchMvpList(OfficialData $data)
    {
        // 本当は Mvp クラスのインスタンスで操作したいところだが、
        // 1回のフェスのデータを全部インスタンス化するのに 3 秒以上
        // かかる有様なのでただの配列として取り扱う
        $tableMvp = Mvp::tableName();
        $query = (new \yii\db\Query())
            ->select("{{{$tableMvp}}}.*")
            ->from("{{{$tableMvp}}}")
            ->andWhere(["{{{$tableMvp}}}.[[data_id]]" => $data->id])
            ->orderBy("{{{$tableMvp}}}.[[id]] ASC");
        $alpha = [];
        $bravo = [];
        foreach ($query->all() as $row) {
            if ($row['color_id'] === '1') {
                $alpha[] = $row['name'];
            } elseif ($row['color_id'] === '2') {
                $bravo[] = $row['name'];
            }
        }
        return [$alpha, $bravo];
    }
}
