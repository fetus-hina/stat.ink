<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170329_125456_rank2 extends Migration
{
    public function up()
    {
        $this->createTable('rank2', [
            'id'        => $this->primaryKey(),
            'group_id'  => $this->pkRef('rank_group2'),
            'rank'      => $this->integer()->notNull()->unique(),
            'key'       => $this->apiKey(),
            'name'      => $this->string(32)->notNull()->unique(),
            'int_base'  => $this->integer()->notNull(),
        ]);

        $group = ArrayHelper::map(
            (new Query())->select(['key', 'id'])->from('rank_group2')->all(),
            'key',
            'id',
        );

        $this->batchInsert('rank2', ['group_id', 'rank', 'key', 'name', 'int_base'], [
            [ $group['c'], 10, 'c-', 'C-',    0 ],
            [ $group['c'], 11, 'c',  'C',   100 ],
            [ $group['c'], 12, 'c+', 'C+',  200 ],
            [ $group['b'], 20, 'b-', 'B-',  300 ],
            [ $group['b'], 21, 'b',  'B',   400 ],
            [ $group['b'], 22, 'b+', 'B+',  500 ],
            [ $group['a'], 30, 'a-', 'A-',  600 ],
            [ $group['a'], 31, 'a',  'A',   700 ],
            [ $group['a'], 32, 'a+', 'A+',  800 ],
            [ $group['s'], 40, 's',  'S',   900 ],
            [ $group['s'], 41, 's+', 'S+', 1000 ],
        ]);
    }

    public function down()
    {
        $this->dropTable('rank2');
    }
}
