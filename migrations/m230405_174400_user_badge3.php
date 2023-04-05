<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m230405_174400_user_badge3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upBossSalmonid();
        $this->upKingSalmonid();
        $this->upEggsecutiveReached();
        $this->upRule();
        $this->upTricolor();
        $this->upSpecial();

        return true;
    }

    private function upBossSalmonid(): void
    {
        $this->createTable('{{%user_badge3_boss_salmonid}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'boss_id' => $this->pkRef('{{%salmon_boss3}}')->notNull(),
            'count' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[user_id]], [[boss_id]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_boss_salmonid}} %s', [
                (new Query())
                    ->select([
                        'user_id' => '{{%salmon3}}.[[user_id]]',
                        'boss_id' => '{{%salmon_boss_appearance3}}.[[boss_id]]',
                        'count' => 'SUM({{%salmon_boss_appearance3}}.[[defeated_by_me]])',
                    ])
                    ->from('{{%salmon3}}')
                    ->innerJoin(
                        '{{%salmon_boss_appearance3}}',
                        '{{%salmon3}}.[[id]] = {{%salmon_boss_appearance3}}.[[salmon_id]]',
                    )
                    ->innerJoin(
                        '{{%salmon_boss3}}',
                        '{{%salmon_boss_appearance3}}.[[boss_id]] = {{%salmon_boss3}}.[[id]]',
                    )
                    ->andWhere([
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon_boss3}}.[[has_badge]]' => true,
                    ])
                    ->groupBy([
                        '{{%salmon3}}.[[user_id]]',
                        '{{%salmon_boss_appearance3}}.[[boss_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );
    }

    private function upKingSalmonid(): void
    {
        $this->createTable('{{%user_badge3_king_salmonid}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'king_id' => $this->pkRef('{{%salmon_king3}}')->notNull(),
            'count' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[user_id]], [[king_id]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_king_salmonid}} %s', [
                (new Query())
                    ->select([
                        'user_id' => '{{%salmon3}}.[[user_id]]',
                        'king_id' => '{{%salmon3}}.[[king_salmonid_id]]',
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%salmon3}}')
                    ->innerJoin(
                        '{{%salmon_king3}}',
                        '{{%salmon3}}.[[king_salmonid_id]] = {{%salmon_king3}}.[[id]]',
                    )
                    ->andWhere([
                        '{{%salmon3}}.[[clear_extra]]' => true,
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                    ])
                    ->groupBy([
                        '{{%salmon3}}.[[user_id]]',
                        '{{%salmon3}}.[[king_salmonid_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );
    }

    private function upEggsecutiveReached(): void
    {
        $this->createTable('{{%user_badge3_eggsecutive_reached}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'stage_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'reached' => $this->integer()->notNull(),
            'PRIMARY KEY ([[user_id]], [[stage_id]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_eggsecutive_reached}} %s', [
                (new Query())
                    ->select([
                        'user_id' => '{{%salmon3}}.[[user_id]]',
                        'stage_id' => '{{%salmon3}}.[[stage_id]]',
                        'reached' => 'MAX({{%salmon3}}.[[title_exp_after]])',
                    ])
                    ->from('{{%salmon3}}')
                    ->innerJoin('{{%salmon_title3}}', '{{%salmon3}}.[[title_after_id]] = {{%salmon_title3}}.[[id]]')
                    ->innerJoin('{{%salmon_map3}}', '{{%salmon3}}.[[stage_id]] = {{%salmon_map3}}.[[id]]')
                    ->andWhere([
                        '{{%salmon3}}.[[is_big_run]]' => false,
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon_title3}}.[[key]]' => 'eggsecutive_vp',
                    ])
                    ->groupBy([
                        '{{%salmon3}}.[[user_id]]',
                        '{{%salmon3}}.[[stage_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );
    }

    private function upRule(): void
    {
        $this->createTable('{{%user_badge3_rule}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'count' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[user_id]], [[rule_id]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_rule}} %s', [
                (new Query())
                    ->select([
                        'user_id' => '{{%battle3}}.[[user_id]]',
                        'rule_id' => '{{%battle3}}.[[rule_id]]',
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%battle3}}')
                    ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
                    ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                    ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                    ->andWhere([
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%result3}}.[[key]]' => 'win',
                    ])
                    ->andWhere(['and',
                        ['<>', '{{%lobby3}}.[[key]]', 'private'],
                        ['<>', '{{%rule3}}.[[key]]', 'tricolor'],
                    ])
                    ->groupBy([
                        '{{%battle3}}.[[user_id]]',
                        '{{%battle3}}.[[rule_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );
    }

    private function upTricolor(): void
    {
        $this->createTable('{{%user_badge3_tricolor}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'role_id' => $this->pkRef('{{%tricolor_role3}}')->notNull(),
            'count' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[user_id]], [[role_id]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_tricolor}} %s', [
                (new Query())
                    ->select([
                        'user_id' => '{{%battle3}}.[[user_id]]',
                        'role_id' => '{{%battle3}}.[[our_team_role_id]]',
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%battle3}}')
                    ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                    ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                    ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
                    ->innerJoin(
                        '{{%tricolor_role3}}',
                        '{{%battle3}}.[[our_team_role_id]] = {{%tricolor_role3}}.[[id]]',
                    )
                    ->andWhere([
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%result3}}.[[key]]' => 'win',
                        '{{%rule3}}.[[key]]' => 'tricolor',
                        '{{%lobby3}}.[[key]]' => 'splatfest_open',
                    ])
                    ->groupBy([
                        '{{%battle3}}.[[user_id]]',
                        '{{%battle3}}.[[our_team_role_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );
    }

    private function upSpecial(): void
    {
        $this->createTable('{{%user_badge3_special}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'special_id' => $this->pkRef('{{%special3}}')->notNull(),
            'count' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[user_id]], [[special_id]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO {{%%user_badge3_special}} %s', [
                (new Query())
                    ->select([
                        'user_id' => '{{%battle3}}.[[user_id]]',
                        'special_id' => '{{%weapon3}}.[[special_id]]',
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%battle3}}')
                    ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                    ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                    ->innerJoin('{{%weapon3}}', '{{%battle3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
                    ->innerJoin('{{%special3}}', '{{%weapon3}}.[[special_id]] = {{%special3}}.[[id]]')
                    ->andWhere([
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%result3}}.[[key]]' => 'win',
                    ])
                    ->andWhere(['and',
                        ['<>', '{{%lobby3}}.[[key]]', 'private'],
                        ['>', '{{%battle3}}.[[special]]', 0],
                    ])
                    ->groupBy([
                        '{{%battle3}}.[[user_id]]',
                        '{{%weapon3}}.[[special_id]]',
                    ])
                    ->createCommand()
                    ->rawSql,
            ]),
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables($this->vacuumTables());

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%user_badge3_boss_salmonid}}',
            '{{%user_badge3_eggsecutive_reached}}',
            '{{%user_badge3_king_salmonid}}',
            '{{%user_badge3_rule}}',
            '{{%user_badge3_special}}',
            '{{%user_badge3_tricolor}}',
        ];
    }
}
