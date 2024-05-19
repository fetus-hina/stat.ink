<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;
use yii\db\Query;

final class m240512_205551_ability_equipment extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->createTable('{{%stat_stealth_jump_equipment3}}', [
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'x_power' => $this->decimal(6, 1)->notNull(),
            'players' => $this->bigInteger()->notNull(),
            'equipments' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[season_id]], [[rule_id]], [[x_power]])',
        ]);

        $select = (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'rule_id' => '{{%rule3}}.[[id]]',
                'x_power' => '(FLOOR({{%battle3}}.[[x_power_after]] / 50.0) * 50.0)',
                'players' => 'COUNT(*)',
                'equipments' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        vsprintf('WHEN %s.%s = %s THEN 1', [
                            $db->quoteTableName('{{%ability3}}'),
                            $db->quoteColumnName('key'),
                            $db->quoteValue('stealth_jump'),
                        ]),
                        'ELSE 0',
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin(
                '{{%season3}}',
                '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]',
            )
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin(
                '{{%battle_player3}}',
                '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]',
            )
            ->innerJoin(
                ['headgear' => '{{%gear_configuration3}}'],
                '{{%battle_player3}}.[[headgear_id]] = {{%headgear}}.[[id]]',
            )
            ->innerJoin(
                ['clothing' => '{{%gear_configuration3}}'],
                '{{%battle_player3}}.[[clothing_id]] = {{%clothing}}.[[id]]',
            )
            ->innerJoin(
                ['shoes' => '{{%gear_configuration3}}'],
                '{{%battle_player3}}.[[shoes_id]] = {{%shoes}}.[[id]]',
            )
            ->innerJoin('{{%ability3}}', '{{%shoes}}.[[ability_id]] = {{%ability3}}.[[id]]')
            ->andWhere([
                '{{%battle3}}.[[has_disconnect]]' => false,
                '{{%battle3}}.[[is_automated]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[use_for_entire]]' => true,
                '{{%battle_player3}}.[[is_me]]' => false,
                '{{%lobby3}}.[[key]]' => 'xmatch',
                '{{%rule3}}.[[key]]' => ['area', 'asari', 'hoko', 'yagura'],
            ])
            ->andWhere(['and',
                ['not', ['{{%battle3}}.[[x_power_after]]' => null]],
                ['not', ['{{%clothing}}.[[ability_id]]' => null]],
                ['not', ['{{%headgear}}.[[ability_id]]' => null]],
                ['not', ['{{%shoes}}.[[ability_id]]' => null]],
            ])
            ->groupBy([
                '{{%season3}}.[[id]]',
                '{{%rule3}}.[[id]]',
                'FLOOR({{%battle3}}.[[x_power_after]] / 50.0)',
            ])
            ->orderBy([
                'season_id' => SORT_ASC,
                'rule_id' => SORT_ASC,
                'x_power' => SORT_ASC,
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName('{{%stat_stealth_jump_equipment3}}'),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_stealth_jump_equipment3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_stealth_jump_equipment3}}',
        ];
    }
}
