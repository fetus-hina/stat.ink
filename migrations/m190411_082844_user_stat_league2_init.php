<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190411_082844_user_stat_league2_init extends Migration
{
    public function safeUp()
    {
        $query = $this->buildSelectQuery();
        $sql = 'INSERT INTO user_stat_league2 ' . $query->createCommand()->rawSql;
        $this->execute($sql);
    }


    public function safeDown()
    {
        $this->truncateTable('user_stat_league2');
    }

    private function buildSelectQuery(): Query
    {
        $select = (new Query())
            ->from('battle2')
            ->andWhere([
                'lobby_id' => $this->getTargetLobbyId(),
                'mode_id' => $this->getTargetModeId(),
                'rule_id' => $this->getTargetRuleId(),
            ])
            ->groupBy(['user_id'])
            ->select(array_merge(
                [
                    'user_id' => 'battle2.user_id',
                    'battles' => 'COUNT(*)',
                    'win_ko' => $this->winCount(true, true),
                    'lose_ko' => $this->winCount(false, true),
                    'win_time' => $this->winCount(true, false),
                    'lose_time' => $this->winCount(false, false),
                    'win_unk' => $this->winCount(true, null),
                    'lose_unk' => $this->winCount(false, null),
                ],
                $this->stats('kill', '[[kill]]'),
                $this->stats('death', '[[death]]'),
                $this->stats('assist', '[[kill_or_assist]] - [[kill]]'),
                [
                    'updated_at' => sprintf("('%s')", date(DATE_ATOM, time())),
                ],
            ));
        return $select;
    }

    private function winCount(bool $filterIsWin, ?bool $filterIsKO): string
    {
        $conds = [];
        $conds[] = 'WHEN [[is_win]] IS NULL THEN 0';
        $conds[] = ($filterIsWin)
            ? 'WHEN [[is_win]] = FALSE THEN 0'
            : 'WHEN [[is_win]] = TRUE THEN 0';
        if ($filterIsKO === null) {
            $conds[] = 'WHEN [[is_knockout]] IS NOT NULL THEN 0';
        } else {
            $conds[] = ($filterIsKO)
                ? 'WHEN [[is_knockout]] = FALSE THEN 0'
                : 'WHEN [[is_knockout]] = TRUE THEN 0';
        }
        $conds[] = 'ELSE 1';
        return sprintf('SUM(CASE %s END)', implode(' ', $conds));
    }

    private function stats(string $baseName, string $column): array
    {
        return [
            "have_{$baseName}" => sprintf(
                'SUM(CASE WHEN %1$s IS NULL THEN 0 ELSE 1 END)',
                $column
            ),
            "total_{$baseName}" => sprintf(
                'SUM(CASE WHEN %1$s IS NULL THEN 0 ELSE %1$s END)',
                $column
            ),
            "total_{$baseName}_with_time" => sprintf('SUM(CASE %s END)', implode(' ', [
                sprintf('WHEN %1$s IS NULL THEN NULL', $column),
                'WHEN [[start_at]] IS NULL OR [[end_at]] IS NULL THEN NULL',
                'ELSE ' . $column,
            ])),
            "total_time_{$baseName}" => sprintf('SUM(CASE %s END)', implode(' ', [
                sprintf('WHEN %1$s IS NULL THEN NULL', $column),
                'WHEN [[start_at]] IS NULL OR [[end_at]] IS NULL THEN NULL',
                sprintf('ELSE (%s - %s)', $this->unixT('[[end_at]]'), $this->unixT('[[start_at]]')),
            ])),
            "min_{$baseName}" => "MIN({$column})",
            "pct5_{$baseName}" => $this->percentile('0.05', $column),
            "q1_4_{$baseName}" => $this->percentile('0.25', $column),
            "median_{$baseName}" => $this->percentile('0.5', $column),
            "q3_4_{$baseName}" => $this->percentile('0.75', $column),
            "pct95_{$baseName}" => $this->percentile('0.95', $column),
            "max_{$baseName}" => "MAX({$column})",
            "stddev_{$baseName}" => "STDDEV_POP({$column})",
        ];
    }

    private function unixT(string $column): string
    {
        return sprintf('EXTRACT(EPOCH FROM %s)', $column);
    }

    private function percentile(string $pos, string $column): string
    {
        return "PERCENTILE_CONT({$pos}) WITHIN GROUP (ORDER BY {$column})";
    }

    private function getTargetLobbyId(): array
    {
        $select = (new Query())
            ->select(['id'])
            ->from('lobby2')
            ->where(['key' => ['squad_2', 'squad_4']]);
        return array_map(
            function (array $row): int {
                return (int)$row['id'];
            },
            $select->all()
        );
    }

    private function getTargetModeId(): array
    {
        $select = (new Query())
            ->select(['id'])
            ->from('mode2')
            ->where(['key' => 'gachi']);
        return array_map(
            function (array $row): int {
                return (int)$row['id'];
            },
            $select->all()
        );
    }

    private function getTargetRuleId()
    {
        $select = (new Query())
            ->select(['id'])
            ->from('rule2')
            ->where(['key' => ['area', 'yagura', 'hoko', 'asari']]);
        return array_map(
            function (array $row): int {
                return (int)$row['id'];
            },
            $select->all()
        );
    }
}
