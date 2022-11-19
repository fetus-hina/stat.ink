<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221119_133607_private_jobs extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%salmon3}}',
            'is_private',
            (string)$this->boolean()->notNull()->defaultValue(false),
        );
        $this->execute('UPDATE {{%salmon3}} SET [[is_private]] = [[job_point]] IS NULL');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%salmon3}}', 'is_private');

        return true;
    }
}
