<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m261117_082408_lobby3_short_name extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->addColumn('{{%lobby3}}', 'short_name', (string)$this->text()->null());

        foreach ($this->getShortNames() as $key => $shortName) {
            $this->update('{{%lobby3}}', ['short_name' => $shortName], ['key' => $key]);
        }

        $this->alterColumn('{{%lobby3}}', 'short_name', 'SET NOT NULL');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropColumn('{{%lobby3}}', 'short_name');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%lobby3}}',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getShortNames(): array
    {
        return [
            'regular' => 'Regular',
            'bankara_challenge' => 'Series',
            'bankara_open' => 'Open',
            'xmatch' => 'X',
            'event' => 'Challenge',
            'splatfest_challenge' => 'Fest Pro',
            'splatfest_open' => 'Fest Open',
            'private' => 'Private',
        ];
    }
}
