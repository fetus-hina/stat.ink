<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221130_083951_add_salmon3_has_broken_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%salmon3}}', [
            'has_broken_data' => $this->boolean()->notNull()->defaultValue(false),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%salmon3}}', 'has_broken_data');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3}}',
        ];
    }
}
