<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230419_054226_weapon3_release_date extends Migration
{
    private const LAUNCH = '2022-01-01T00:00:00+00:00';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%weapon3}}',
            'release_at',
            (string)$this->timestampTZ(0)->notNull()->defaultValue(self::LAUNCH),
        );

        $this->update(
            '{{%weapon3}}',
            ['release_at' => '2022-12-01T00:00:00+00:00'],
            [
                'key' => [
                    'bucketslosher_deco',
                    'carbon_deco',
                    'momiji',
                    'nova_neo',
                    'pablo_hue',
                    'prime_collabo',
                    'promodeler_rg',
                    'rpen_5h',
                    'spaceshooter',
                    'splatspinner_collabo',
                    'sputtery_hue',
                    'sshooter_collabo',
                    'wideroller',
                ],
            ],
        );

        $this->update(
            '{{%weapon3}}',
            ['release_at' => '2023-03-01T00:00:00+00:00'],
            [
                'key' => [
                    '96gal_deco',
                    'bold_neo',
                    'clashblaster_neo',
                    'hissen_hue',
                    'jetsweeper_custom',
                    'l3reelgun_d',
                    'nzap89',
                    'rapid_deco',
                    'sharp_neo',
                    'splatcharger_collabo',
                    'splatroller_collabo',
                    'splatscope_collabo',
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
        $this->dropColumn('{{%weapon3}}', 'release_at');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
        ];
    }
}
