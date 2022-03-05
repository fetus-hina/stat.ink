<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m171002_124420_link_from_others extends Migration
{
    public function up()
    {
        $this->createTable('link_mode', [
            'id'    => $this->primaryKey(),
            'key'   => $this->apiKey(),
            'name'  => $this->string(64)->notNull(),
            'rank'  => $this->integer()->notNull()->unique(),
        ]);
        $this->batchInsert('link_mode', [ 'key', 'name', 'rank' ], [
            [
                'anonymize',
                'Anonymize, don\'t link from other user\'s results',
                10,
            ],
            [
                'in_game',
                'Don\'t anonymize, display in-game name',
                20,
            ],
            [
                'statink',
                'Don\'t anonymize, display stat.ink\'s name',
                30,
            ],
        ]);
        $inGame = (new Query())
            ->select(['id'])
            ->from('link_mode')
            ->where(['key' => 'in_game'])
            ->scalar();
        $this->execute(
            'ALTER TABLE {{user}} ' .
            'ADD COLUMN [[link_mode_id]] INTEGER NOT NULL ' .
            'DEFAULT ' . $this->db->quoteValue($inGame) . ' ' .
            'REFERENCES {{link_mode}}([[id]])'
        );
    }

    public function down()
    {
        $this->dropColumn('{{user}}', 'link_mode_id');
        $this->dropTable('link_mode');
    }
}
