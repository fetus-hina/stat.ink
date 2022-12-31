<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

class m190724_173449_freshness extends Migration
{
    public function up()
    {
        $s = parent::up();
        if ($s !== false) {
            $this->execute('VACUUM ANALYZE [[freshness2]]');
        }
        return $s;
    }

    public function safeUp()
    {
        $this->addColumn(
            'battle2',
            'freshness',
            (string)$this->decimal(3, 1)->check('[[freshness]] BETWEEN 0.0 AND 99.9'),
        );
        $this->createTable('freshness2', [
            'id'    => $this->primaryKey(),
            'name'  => $this->string(12)->notNull(),
            'color' => $this->string(8)->notNull(),
            'range' => 'numrange NOT NULL',
            'EXCLUDE USING gist ([[range]] WITH &&)',
        ]);
        $this->batchInsert('freshness2', ['name', 'color', 'range'], [
            ['Dry',         'grey',     $this->mkrange(null, 5.0)],
            ['Raw',         'green',    $this->mkrange(5.0, 10.0)],
            ['Fresh',       'orange',   $this->mkrange(10.0, 15.0)],
            ['SUPERFRESH!', 'silver',   $this->mkrange(15.0, 20.0)],
            ['SUPERFRESH!', 'white',    $this->mkrange(20.0, 50.0)],
            ['SUPERFRESH!', 'gold',     $this->mkrange(50.0, null)],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('freshness2');
        $this->dropColumn('battle2', 'freshness');
    }

    public function mkrange(?float $val1, ?float $val2): Expression
    {
        return new Expression(sprintf(
            '%s::numrange',
            $this->db->quoteValue(sprintf(
                '[%s,%s)',
                $val1 === null ? '' : sprintf('%.1f', $val1),
                $val2 === null ? '' : sprintf('%.1f', $val2),
            )),
        ));
    }
}
