<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m250903_101749_s3_v10_1_0 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->upVersion3(
            '10.1',
            'v10.1.x',
            '10.1.0',
            'v10.1.0',
            new DateTimeImmutable('2025-09-04T10:10:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->downVersion3('10.1.0', '10.0.1');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%splatoon_version3}}',
            '{{%splatoon_version_group3}}',
        ];
    }
}
