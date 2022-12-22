<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221218_055408_battle3_signal extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%battle3}}', [
            'signal' => $this->integer()->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%battle3}}', [
            'signal',
        ]);

        return true;
    }
}
