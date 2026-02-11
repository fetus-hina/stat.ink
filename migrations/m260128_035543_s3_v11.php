<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m260128_035543_s3_v11 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->upVersion3(
            '11.0',
            'v11.0.x',
            '11.0.0',
            'v11.0.0',
            new DateTimeImmutable('2026-01-29T10:10:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->downVersion3('11.0.0', '10.1.0');

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
