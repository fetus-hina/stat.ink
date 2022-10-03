<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221003_100550_fest_clout extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%battle3}}', [
            'clout_before' => $this->integer()->null(),
            'clout_after' => $this->integer()->null(),
            'clout_change' => $this->integer()->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%battle3}}', ['clout_before', 'clout_after', 'clout_change']);

        return true;
    }
}
