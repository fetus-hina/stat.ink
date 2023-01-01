<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use Yii;
use app\models\Region;
use app\models\Splatfest;
use app\models\SplatfestBattleSummary;
use app\models\SplatfestTeam;
use yii\console\Controller;

class SplatfestController extends Controller
{
    public function actionUpdateAuto()
    {
        $now = (int)(@$_SERVER['REQUEST_TIME'] ?: time());

        $query = Splatfest::find()
            ->with(['region'])
            ->andWhere(['<=', '{{splatfest}}.[[start_at]]', date('Y-m-d\TH:i:sP', $now)])
            ->andWhere(['>', '{{splatfest}}.[[end_at]]', date('Y-m-d\TH:i:sP', $now - 120)])
            ->orderBy('{{splatfest}}.[[id]] ASC');
        foreach ($query->all() as $fest) {
            try {
                $this->actionUpdate($fest->region->key, $fest->order);
            } catch (\Throwable $e) {
                echo 'Catch exception: ' . $e->getMessage() . "\n";
            }
        }
    }

    public function actionUpdate($region, $number)
    {
        $transaction = Yii::$app->db->beginTransaction();

        if (!$region = Region::findOne(['key' => $region])) {
            printf("Unknown region: %s\n", $region);
            return 1;
        }
        if (!$fest = Splatfest::findOne(['region_id' => $region->id, '[[order]]' => $number])) {
            printf("Unknown number: %s (region = %s)\n", $number, $region->key);
            return 1;
        }
        printf("Target #%d (Region=%s, Number=%d)\n", $fest->id, $region->key, $fest->order);

        $alpha = SplatfestTeam::findOne(['fest_id' => $fest->id, 'team_id' => 1]);
        $bravo = SplatfestTeam::findOne(['fest_id' => $fest->id, 'team_id' => 2]);
        if (!$alpha || !$bravo || $alpha->color_hue === null || $bravo->color_hue === null) {
            printf("    > Team information is not ready\n");
            return 1;
        }

        printf("    > cleanup ...\n");
        SplatfestBattleSummary::deleteAll(['fest_id' => $fest->id]);

        $this->createBattleSummaryTmpTimestamp($fest);
        $this->createBattleSummaryTmpTable('tmp_summary_a', $fest, $alpha->color_hue, $bravo->color_hue);
        $this->createBattleSummaryTmpTable('tmp_summary_b', $fest, $bravo->color_hue, $alpha->color_hue);
        $this->createBattleSummary($fest);
        $this->dropTmpTable();

        $transaction->commit();

        return 0;
    }

    private function createBattleSummaryTmpTimestamp(Splatfest $fest)
    {
        $now = (int)(@$_SERVER['REQUEST_TIME'] ?: time());
        $start_at = strtotime($fest->start_at);
        $end_at = min(strtotime($fest->end_at), $now);

        // サマリ間隔に合うように補正
        $start_at = (int)floor($start_at / 120) * 120;
        $end_at = (int)floor($end_at / 120) * 120;

        echo "    > create temporary table tmp_summary_ts ...\n";
        Yii::$app->db->createCommand(
            'CREATE TEMPORARY TABLE {{tmp_summary_ts}} ' .
            '( [[timestamp]] TIMESTAMP (0) WITH TIME ZONE NOT NULL PRIMARY KEY )',
        )->execute();

        echo "    > insert tmp_summary_ts ...\n";
        if ($start_at > $now) {
            echo "    >> skip (future)\n";
        } else {
            Yii::$app->db->createCommand()->batchInsert('tmp_summary_ts', ['timestamp'], array_map(
                fn ($at) => [
                        date('Y-m-d\TH:i:sP', $at),
                    ],
                range($start_at, $end_at, 120),
            ))->execute();
        }
    }

    private function createBattleSummaryTmpTable($tableName, Splatfest $fest, $hueMy, $hueHis)
    {
        $t1 = microtime(true);
        printf('    > create temporary table %s ... ', $tableName);

        $timestamp_ = 'CEILING(EXTRACT(EPOCH FROM {{battle}}.[[end_at]]) / 120) * 120';
        $timestamp = "TO_TIMESTAMP({$timestamp_})";
        $where = [
            '{{lobby}}.[[key]] = :lobby_fest',
            '{{rule}}.[[key]] = :rule_nawabari',
            '{{battle}}.[[is_automated]] = TRUE',
            '{{battle}}.[[is_win]] IS NOT NULL',
            '{{battle}}.[[end_at]] >= :fest_start',
            '{{battle}}.[[end_at]] < :fest_end',
            $this->createHueRange('{{battle}}.[[my_team_color_hue]]', $hueMy),
            $this->createHueRange('{{battle}}.[[his_team_color_hue]]', $hueHis),
        ];
        $bind = [
            ':lobby_fest' => 'fest',
            ':rule_nawabari' => 'nawabari',
            ':fest_start' => $fest->start_at,
            ':fest_end' => $fest->end_at,
        ];
        $select =
            'SELECT ' . implode(', ', [
                $timestamp . ' AS [[timestamp]]',
                'SUM(CASE WHEN {{battle}}.[[is_win]] THEN 1 ELSE 0 END) AS [[win]]',
                'SUM(CASE WHEN {{battle}}.[[is_win]] THEN 0 ELSE 1 END) AS [[lose]]',
            ]) . ' ' .
            'FROM {{battle}} ' .
            'INNER JOIN {{lobby}} ON {{battle}}.[[lobby_id]] = {{lobby}}.[[id]] ' .
            'INNER JOIN {{rule}} ON {{battle}}.[[rule_id]] = {{rule}}.[[id]] ' .
            'WHERE (' . implode(') AND (', $where) . ') ' .
            'GROUP BY ' . $timestamp;
        $sql = sprintf(
            'CREATE TEMPORARY TABLE {{%s}} AS %s',
            $tableName,
            $select,
        );
        $command = Yii::$app->db->createCommand($sql)->bindValues($bind);
        $command->execute();

        $t2 = microtime(true);
        printf(" done (%.3fs)\n", $t2 - $t1);
    }

    private function createBattleSummary(Splatfest $fest)
    {
        echo "    > insert to splatfest_battle_summary ...\n";
        $select =
            'SELECT ' . implode(', ', [
                ':fest_id AS [[fest_id]]',
                '{{ts}}.[[timestamp]]',
                'COALESCE({{a}}.[[win]], 0) AS [[alpha_win]]',
                'COALESCE({{a}}.[[lose]], 0) AS [[alpha_lose]]',
                'COALESCE({{b}}.[[win]], 0) AS [[bravo_win]]',
                'COALESCE({{b}}.[[lose]], 0) AS [[bravo_lose]]',
                ':now AS [[summarized_at]]',
            ]) . ' ' .
            'FROM {{tmp_summary_ts}} AS {{ts}}' .
            'LEFT JOIN {{tmp_summary_a}} AS {{a}} ON {{ts}}.[[timestamp]] = {{a}}.[[timestamp]] ' .
            'LEFT JOIN {{tmp_summary_b}} AS {{b}} ON {{ts}}.[[timestamp]] = {{b}}.[[timestamp]] ';
        Yii::$app->db
            ->createCommand('INSERT INTO {{splatfest_battle_summary}} ' . $select)
            ->bindValues([
                ':fest_id' => $fest->id,
                ':now' => date('Y-m-d\TH:i:sP', (int)(@$_SERVER['REQUEST_TIME'] ?: time())),
            ])
            ->execute();
    }

    private function createHueRange($column, $hue, $permitError = 6)
    {
        $hue = (int)$hue;
        $low = $hue - $permitError;
        $high = $hue + $permitError;
        if ($low < 0) {
            $low += 360;
            return "(({$column} <= {$high}) OR ({$column} >= {$low}))";
        } elseif ($high > 360) {
            $high -= 360;
            return "(($column} >= {$low}) OR ({$column} <= {$high}))";
        } else {
            return "({$column} BETWEEN {$low} AND {$high})";
        }
    }

    private function dropTmpTable()
    {
        foreach (['tmp_summary_ts', 'tmp_summary_a', 'tmp_summary_b'] as $tableName) {
            $t1 = microtime(true);
            printf('    > drop temporary table %s ... ', $tableName);
            $sql = sprintf('DROP TABLE {{%s}}', $tableName);
            Yii::$app->db->createCommand($sql)->execute();
            $t2 = microtime(true);
            printf(" done (%.3fs)\n", $t2 - $t1);
        }
    }
}
