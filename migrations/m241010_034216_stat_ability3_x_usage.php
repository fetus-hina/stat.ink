<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m241010_034216_stat_ability3_x_usage extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $columns = [
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'range_id' => $this->pkRef('{{%stat_weapon3_x_usage_range}}')->notNull(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
            'players' => $this->bigInteger()->notNull(),
        ];

        $abilities = ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('{{%ability3}}')
                ->orderBy(['rank' => SORT_ASC])
                ->all($this->db),
            'key',
            'id',
        );

        $zero = new Expression('0.0::double precision');
        foreach (array_keys($abilities) as $key) {
            $columns["{$key}_players"] = $this->bigInteger()->notNull()->defaultValue(0);
            $columns["{$key}_avg"] = $this->double()->notNull()->defaultValue($zero);
        }

        $this->createTable('{{%stat_ability3_x_usage}}', array_merge($columns, [
            'PRIMARY KEY ([[season_id]], [[rule_id]], [[range_id]], [[weapon_id]])',
        ]));

        $columns = [
            'season_id' => '{{%season3}}.[[id]]',
            'rule_id' => '{{%battle3}}.[[rule_id]]',
            'range_id' => '{{%stat_weapon3_x_usage_range}}.[[id]]',
            'weapon_id' => '{{%battle_player3}}.[[weapon_id]]',
            'players' => 'COUNT(*)',
        ];

        $select = (new Query())
            ->from('{{%battle3}}')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin(
                '{{%stat_weapon3_x_usage_range}}',
                '{{%battle3}}.[[x_power_before]] <@ {{%stat_weapon3_x_usage_range}}.[[x_power_range]]',
            )
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJOin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%rule3}}.[[key]]' => ['area', 'yagura', 'hoko', 'asari'],
                    '{{%lobby3}}.[[key]]' => 'xmatch',
                ],
                ['not', ['{{%battle3}}.[[x_power_before]]' => null]],
                ['not', ['{{%battle_player3}}.[[weapon_id]]' => null]],
            ])
            ->groupBy([
                $columns['season_id'],
                $columns['rule_id'],
                $columns['range_id'],
                $columns['weapon_id'],
            ]);

        foreach ($abilities as $key => $id) {
            $tmpTable = "pa_{$key}";
            $select->leftJoin(
                [$tmpTable => '{{%battle_player_gear_power3}}'],
                ['and',
                    "{{%battle_player3}}.[[id]] = {{{$tmpTable}}}.[[player_id]]",
                    "{{{$tmpTable}}}.[[ability_id]] = {$id}",
                ],
            );
            $value = vsprintf('(CASE WHEN %s THEN %s ELSE 0 END)', [
                sprintf('{{%s}}.[[id]] IS NOT NULL', $tmpTable),
                sprintf('{{%s}}.[[gear_power]]', $tmpTable),
            ]);

            $columns["{$key}_players"] = "SUM(CASE WHEN {{{$tmpTable}}}.[[id]] IS NOT NULL THEN 1 ELSE 0 END)";
            $columns["{$key}_avg"] = "AVG({$value})";
        }

        $select->select($columns);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            $this->db->quoteTableName('{{%stat_ability3_x_usage}}'),
            implode(', ', array_map(
                $this->db->quoteColumnName(...),
                array_keys($columns),
            )),
            $select->createCommand()->rawSql,
        ]);

        $this->execute($sql);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_ability3_x_usage}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_ability3_x_usage}}',
        ];
    }
}
