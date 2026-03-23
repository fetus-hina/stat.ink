<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m260318_041638_s3_11_1_0 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->upVersion3(
            '11.1',
            'v11.1.x',
            '11.1.0',
            'v11.1.0',
            new DateTimeImmutable('2026-03-19T10:10:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->downVersion3('11.1.0', '11.0.1');

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
