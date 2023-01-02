<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m191216_105410_add_disconnect_to_battle2 extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            'battle2',
            'has_disconnect',
            $this->boolean()->notNull()->defaultValue(false),
        );
    }

    public function safeDown()
    {
        $this->dropColumn('battle2', 'has_disconnect');
    }
}
