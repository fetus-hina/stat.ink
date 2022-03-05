<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use yii\db\Query;

class m151208_102647_automated_flag extends Migration
{
    public function up()
    {
        $this->createTable('agent_attribute', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string(64)->notNull()->unique(),
            'is_automated'  => $this->boolean()->notNull(),
        ]);
        $this->batchInsert(
            'agent_attribute',
            ['name', 'is_automated'],
            [
                ['IkaLog', true],
                ['IkaRec', false],
                ['IkaRecord', false],
                ['TakoLog', true],
            ]
        );

        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[is_automated]] BOOLEAN NOT NULL DEFAULT FALSE');
        $this->execute(
            'UPDATE {{battle}} SET [[is_automated]] = TRUE ' .
            preg_replace(
                '/^.+ FROM\s/',
                'FROM ',
                ((new Query())
                    ->from('agent')
                    ->andWhere('{{battle}}.[[agent_id]] = {{agent}}.[[id]]')
                    ->andWhere(['{{agent}}.[[name]]' => ['IkaLog', 'TakoLog']])
                    ->createCommand()
                    ->rawSql)
            )
        );
        $this->execute('VACUUM {{battle}}');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[is_automated]]');
        $this->dropTable('agent_attribute');
    }
}
