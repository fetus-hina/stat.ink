<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m261127_015040_s3_12_0_1 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->upVersion3(
            '12.0',
            'v12.0.x',
            '12.0.1',
            'v12.0.1',
            new DateTimeImmutable('2026-11-27T10:10:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->downVersion3('12.0.1', '12.0.0');

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
