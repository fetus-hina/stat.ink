<?php
use yii\db\Migration;

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
                ((new \yii\db\Query())
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
