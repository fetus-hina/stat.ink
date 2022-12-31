<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170728_123204_user_weapon2 extends Migration
{
    public function up()
    {
        $this->createTable('user_weapon2', [
            'user_id'       => $this->integer()->notNull(),
            'weapon_id'     => $this->integer()->NotNull(),
            'battles'       => $this->bigInteger()->notNull()->check('[[battles]] >= 0'),
            'last_used_at'  => $this->timestampTZ()->notNull(),
            'PRIMARY KEY ([[user_id]], [[weapon_id]])',
            'FOREIGN KEY ([[user_id]]) REFERENCES {{user}}([[id]])',
            'FOREIGN KEY ([[weapon_id]]) REFERENCES {{weapon2}}([[id]])',
        ]);
        $this->execute('CREATE INDEX ON {{user_weapon2}} (' . implode(', ', [
            '[[user_id]] ASC',
            '[[battles]] DESC',
        ]) . ')');
        $select = 'SELECT ' .
            implode(', ', [
                '[[user_id]]',
                '[[weapon_id]]',
                'COUNT(*) AS [[battles]]',
                'MAX(CASE WHEN [[end_at]] IS NOT NULL THEN [[end_at]] ELSE [[created_at]] END) AS [[last_used_at]]',
            ]) . ' ' .
            'FROM {{battle2}} ' .
            'WHERE ({{battle2}}.[[weapon_id]] IS NOT NULL) ' .
            'GROUP BY {{battle2}}.[[user_id]], {{battle2}}.[[weapon_id]]';

        $this->execute(
            'INSERT INTO {{user_weapon2}} ( [[user_id]], [[weapon_id]], [[battles]], [[last_used_at]] ) ' .
            $select . ' ' .
            'ON CONFLICT ([[user_id]], [[weapon_id]]) DO UPDATE SET ' .
            '[[battles]] = EXCLUDED.[[battles]], ' .
            '[[last_used_at]] = EXCLUDED.[[last_used_at]] ',
        );
    }

    public function down()
    {
        $this->dropTable('user_weapon2');
    }
}
