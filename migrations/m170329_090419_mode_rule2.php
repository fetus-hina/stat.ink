<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170329_090419_mode_rule2 extends Migration
{
    public function up()
    {
        $this->createTable('mode_rule2', [
            'mode_id' => $this->pkRef('mode2'),
            'rule_id' => $this->pkRef('rule2'),
            'PRIMARY KEY([[mode_id]], [[rule_id]])',
        ]);
        $m = $this->getModes();
        $r = $this->getRules();
        $this->insert('mode_rule2', [
            'mode_id' => $m['regular'],
            'rule_id' => $r['nawabari'],
        ]);
    }

    public function down()
    {
        $this->dropTable('mode_rule2');
    }

    protected function getModes(): array
    {
        return ArrayHelper::map(
            ((new Query())->select(['key', 'id'])->from('mode2')->all()),
            'key',
            'id',
        );
    }

    protected function getRules(): array
    {
        return ArrayHelper::map(
            ((new Query())->select(['key', 'id'])->from('rule2')->all()),
            'key',
            'id',
        );
    }
}
