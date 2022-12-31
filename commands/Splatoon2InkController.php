<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Curl\Curl;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Map2;
use app\models\Rule2;
use app\models\SalmonMap2;
use app\models\SalmonSchedule2;
use app\models\SalmonWeapon2;
use app\models\Schedule2;
use app\models\ScheduleMap2;
use app\models\ScheduleMode2;
use app\models\Weapon2;
use stdClass;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Json;

class Splatoon2InkController extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        $status = 0;
        $status |= $this->actionUpdateSchedule();
        $status |= $this->actionUpdateCoopSchedule();
        return $status === 0 ? 0 : 1;
    }

    public function actionUpdateSchedule(): int
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
        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            $transaction->rollBack();
            return 1;
        }
    }

    // スケジュール 実装 {{{
    private function importSchedules(ScheduleMode2 $mode, array $list)
    {
        usort($list, fn (stdClass $a, stdClass $b): int => $a->start_time <=> $b->start_time);
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
                'id',
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
                'id',
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
                    fn (stdClass $stage): int => $maps[$stage->id] ?? -1,
                    $stages,
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
    // }}}

    public function actionUpdateCoopSchedule(): int
    {
        $json = $this->queryJson('https://splatoon2.ink/data/coop-schedules.json');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->importCoopSchedules($json->details)) {
                $transaction->commit();
                return 0;
            }
        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        }
        $transaction->rollBack();
        return 1;
    }

    // スケジュール 実装 {{{
    private function importCoopSchedules(array $list): bool
    {
        usort($list, fn (stdClass $a, stdClass $b): int => $a->start_time <=> $b->start_time);
        $ret = true;
        foreach ($list as $schedule) {
            $ret &= $this->importCoopSchedule($schedule);
        }
        return $ret;
    }

    private function importCoopSchedule(stdClass $json): bool
    {
        $startTime = $this->dateTimeFromTimestamp($json->start_time);
        $endTime = $this->dateTimeFromTimestamp($json->end_time);

        echo "Schedule: " . $startTime->format(DateTime::ATOM) . ' - ' . $endTime->format(DateTime::ATOM) . "\n";

        // 期間の重なるスケジュールを全件取得
        $schedules = SalmonSchedule2::find()
            ->andWhere(['and',
                ['<=', 'start_at', $endTime->format(DateTime::ATOM)],
                ['>=', 'end_at', $startTime->format(DateTime::ATOM)],
            ])
            ->all();
        $schedule = null; // 開始・終了が一致するスケジュール
        if ($schedules) {
            foreach ($schedules as $_) {
                if (
                    @strtotime($_['start_at']) === $startTime->getTimestamp() &&
                    @strtotime($_['end_at']) === $endTime->getTimestamp()
                ) {
                    $schedule = $_;
                    echo "Found same term schedule data, id = " . $_->id . "\n";
                } else {
                    // 開始・終了は一致しないが期間が重複しているのはおかしなデータ
                    echo "Found invalid term schedule data, id = " . $_->id . "\n";
                    if ($_->delete() === false) {
                        echo "Could not delete the data.\n";
                        return false;
                    }
                }
            }
        }

        if (!$schedule) {
            $schedule = Yii::createObject([
                'class' => SalmonSchedule2::class,
                'map_id' => null,
                'start_at' => $startTime->format(DateTime::ATOM),
                'end_at' => $endTime->format(DateTime::ATOM),
            ]);
        }

        $map = SalmonMap2::findOne(['name' => $json->stage->name ?? '?']);
        if (!$map) {
            echo "Unknown stage: " . ($json->stage->name ?? '??') . "\n";
            return false;
        }

        if ($schedule->map_id != $map->id) {
            $schedule->map_id = $map->id;
            if (!$schedule->save()) {
                echo "Could not create/update schedule\n";
                return false;
            }
            echo "Schedule created.\n";
        }

        $jsonWeapons = array_filter(
            array_map(
                function ($weapon): ?int {
                    $id = (is_object($weapon) && $weapon instanceof stdClass)
                        ? ($weapon->id ?? null)
                        : null;
                    return ($id !== null && preg_match('/^\d+$/', $id))
                        ? (int)$id
                        : null;
                },
                $json->weapons,
            ),
            fn (?int $id): bool => $id !== null,
        );

        $currentWeapons = array_map(
            fn (SalmonWeapon2 $weapon): int => $weapon->weapon->splatnet,
            SalmonWeapon2::find()
                ->with('weapon')
                ->andWhere(['schedule_id' => $schedule->id])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
        );

        if ($jsonWeapons === $currentWeapons) {
            echo "Weapon data is up to date.\n";
            return true;
        }

        echo "Create weapons data\n";
        SalmonWeapon2::deleteAll(['schedule_id' => $schedule->id]);

        foreach ($jsonWeapons as $weaponId) {
            $weapon = Weapon2::findOne(['splatnet' => $weaponId]);
            if (!$weapon) {
                echo "Weapon not found (splatnet id = {$weaponId})\n";
                return false;
            }

            echo "  {$weaponId} => {$weapon->id} ({$weapon->key})\n";

            $model = Yii::createObject([
                'class' => SalmonWeapon2::class,
                'schedule_id' => $schedule->id,
                'weapon_id' => $weapon->id,
            ]);
            if (!$model->save()) {
                echo "  failed to save!\n";
                return false;
            }
        }
        echo "Updated.\n";

        return true;
    }
    // }}}

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
            throw new \Exception("Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}");
        }
        return Json::decode($curl->rawResponse, false);
    }

    private function dateTimeFromTimestamp(int $timestamp): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimestamp($timestamp)
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone));
    }
}
