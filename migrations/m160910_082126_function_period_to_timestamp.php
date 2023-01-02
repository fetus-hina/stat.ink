<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160910_082126_function_period_to_timestamp extends Migration
{
    public function up()
    {
        $this->execute(
            'CREATE FUNCTION {{period_to_timestamp}} ( IN INTEGER ) ' .
            'RETURNS TIMESTAMP(0) WITH TIME ZONE ' .
            'COST 1 ' .
            'RETURNS NULL ON NULL INPUT ' .
            'IMMUTABLE ' .
            'LANGUAGE SQL ' .
            'SECURITY INVOKER ' .
            'AS ' . $this->db->quoteValue('SELECT TO_TIMESTAMP($1 * 14400 + 7200)'),
        );
    }

    public function down()
    {
        $this->execute('DROP FUNCTION {{period_to_timestamp}} ( IN INTEGER )');
    }
}
