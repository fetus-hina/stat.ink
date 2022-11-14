<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221108_052005_s3sits_link extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('agent_attribute', [
            'name' => 's3si.ts',
            'is_automated' => true,
            'link_url' => 'https://github.com/spacemeowx2/s3si.ts',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('agent_attribute', ['name' => 's3si.ts']);

        return true;
    }
}
