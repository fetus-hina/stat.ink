<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170718_073002_user_region extends Migration
{
    public function up()
    {
        $this->addColumn(
            'user',
            'region_id',
            sprintf(
                'INTEGER NOT NULL DEFAULT %d REFERENCES {{region}}([[id]])',
                (new Query())
                    ->select('id')
                    ->from('region')
                    ->where(['key' => 'jp'])
                    ->scalar(),
            ),
        );
    }

    public function down()
    {
        $this->dropColumn('user', 'region_id');
    }
}
