<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230718_061719_bigrun_stage extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%map3}}',
            'bigrun',
            (string)$this->boolean()->notNull()->defaultValue(false),
        );

        $this->update(
            '{{%map3}}',
            ['bigrun' => true],
            [
                'key' => [
                    'sumeshi',
                    'amabi',
                    'mategai',
                ],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%map3}}', 'bigrun');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
        ];
    }
}
