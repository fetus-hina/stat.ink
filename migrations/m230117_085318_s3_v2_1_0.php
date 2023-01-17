<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m230117_085318_s3_v2_1_0 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upVersion3(
            '2.1',
            'v2.1.x',
            '2.1.0',
            'v2.1.0',
            new DateTimeImmutable('2023-01-18T10:00:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downVersion3('2.1.0', '2.0.1');

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
