<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m230914_194301_s3_501 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upVersion3(
            '5.0',
            'v5.0.x',
            '5.0.1',
            'v5.0.1',
            new DateTimeImmutable('2023-09-15T09:00:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downVersion3('5.0.1', '5.0.0');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%splatoon_version3}}',
            '{{%splatoon_version_group3}}',
        ];
    }
}
