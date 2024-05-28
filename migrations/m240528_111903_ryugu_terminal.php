<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m240528_111903_ryugu_terminal extends Migration
{
    use AutoKey;

    private const KEY = 'ryugu';
    private const NAME = 'Lemuria Hub';
    private const NAME_SHORT = 'Hub';
    private const RELEASE_AT = '2024-06-01T00:00:00+00:00';
    private const ESTIMATED_ID = '24';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3}}', [
            'key' => self::KEY,
            'name' => self::NAME,
            'short_name' => self::NAME_SHORT,
            'release_at' => self::RELEASE_AT,
            'bigrun' => false,
        ]);

        $id = $this->key2id('{{%map3}}', self::KEY);
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [$id, self::name2key3(self::NAME)],
            [$id, self::name2key3(self::NAME_SHORT)],
            [$id, self::ESTIMATED_ID],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', self::KEY);

        $this->delete('{{%map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%map3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
            '{{%map3_alias}}',
        ];
    }
}
