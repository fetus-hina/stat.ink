<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230406_222827_salmon_contest extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%salmon3}}',
            'is_eggstra_work',
            (string)$this->boolean()->notNull()->defaultValue(false),
        );

        $this->addColumn(
            '{{%salmon_wave3}}',
            'danger_rate',
            (string)$this->decimal(5, 1),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%salmon3}}', 'is_eggstra_work');
        $this->dropColumn('{{%salmon_wave3}}', 'danger_rate');

        return true;
    }
}
