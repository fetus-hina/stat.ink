<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m230105_114920_x_matching_group extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%x_matching_group_version3}}', [
            'id' => $this->primaryKey(),
            'minimum_version' => $this->string(16)->notNull()->unique(),
        ]);

        $this->insert('{{%x_matching_group_version3}}', [
            'minimum_version' => '2.0.0',
        ]);
        $versionId = $this->key2id('{{%x_matching_group_version3}}', '2.0.0', 'minimum_version');

        $this->createTable('{{%x_matching_group3}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'short_name' => $this->string(8)->notNull()->unique(),
            'color' => $this->char(6)->notNull(),
            'rank' => $this->integer()->notNull()->unique(),
        ]);

        $this->batchInsert('{{%x_matching_group3}}', ['short_name', 'name', 'color', 'rank'], [
            ['A+', 'A Long', 'ff7f7f', 1000],
            ['A-', 'A Short', 'ff7f7f', 1020],
            ['B', 'B', 'ffbf7f', 1110],
            ['C+', 'C Long', 'ffff7f', 1200],
            ['C-', 'C Short', 'ffff7f', 1220],
            ['D+', 'D Long', 'bfff7f', 1300],
            ['D-', 'D Short', 'bfff7f', 1320],
            ['E+', 'E Long', '7fbfff', 1400],
            ['E-', 'E Short', '7fbfff', 1420],
        ]);

        /**
         * @var array<string, int> $groupIds
         */
        $groupIds = ArrayHelper::map(
            (new Query())->select('*')->from('{{%x_matching_group3}}')->all(),
            'short_name',
            'id',
        );

        /**
         * @var array<string, int> $weaponIds
         */
        $weaponIds = ArrayHelper::map(
            (new Query())->select('*')->from('{{%weapon3}}')->all(),
            'key',
            'id',
        );

        $this->createTable('{{%x_matching_group_weapon3}}', [
            'version_id' => $this->pkRef('{{%x_matching_group_version3}}')->notNull(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
            'group_id' => $this->pkRef('{{%x_matching_group3}}')->notNull(),

            'PRIMARY KEY ([[version_id]], [[weapon_id]])',
        ]);

        $this->batchInsert(
            '{{%x_matching_group_weapon3}}',
            ['version_id', 'weapon_id', 'group_id'],
            [
                [$versionId, $weaponIds['liter4k'], $groupIds['A+']],
                [$versionId, $weaponIds['liter4k_scope'], $groupIds['A+']],
                [$versionId, $weaponIds['splatcharger'], $groupIds['A+']],
                [$versionId, $weaponIds['splatscope'], $groupIds['A+']],

                [$versionId, $weaponIds['bamboo14mk1'], $groupIds['A-']],
                [$versionId, $weaponIds['rpen_5h'], $groupIds['A-']],
                [$versionId, $weaponIds['soytuber'], $groupIds['A-']],
                [$versionId, $weaponIds['squiclean_a'], $groupIds['A-']],

                [$versionId, $weaponIds['barrelspinner'], $groupIds['B']],
                [$versionId, $weaponIds['hydra'], $groupIds['B']],
                [$versionId, $weaponIds['kugelschreiber'], $groupIds['B']],
                [$versionId, $weaponIds['tristringer'], $groupIds['B']],

                [$versionId, $weaponIds['96gal'], $groupIds['C+']],
                [$versionId, $weaponIds['bottlegeyser'], $groupIds['C+']],
                [$versionId, $weaponIds['dualsweeper'], $groupIds['C+']],
                [$versionId, $weaponIds['h3reelgun'], $groupIds['C+']],
                [$versionId, $weaponIds['jetsweeper'], $groupIds['C+']],
                [$versionId, $weaponIds['kelvin525'], $groupIds['C+']],
                [$versionId, $weaponIds['lact450'], $groupIds['C+']],
                [$versionId, $weaponIds['nautilus47'], $groupIds['C+']],
                [$versionId, $weaponIds['prime'], $groupIds['C+']],
                [$versionId, $weaponIds['prime_collabo'], $groupIds['C+']],
                [$versionId, $weaponIds['spaceshooter'], $groupIds['C+']],
                [$versionId, $weaponIds['splatspinner'], $groupIds['C+']],
                [$versionId, $weaponIds['splatspinner_collabo'], $groupIds['C+']],

                [$versionId, $weaponIds['52gal'], $groupIds['C-']],
                [$versionId, $weaponIds['bold'], $groupIds['C-']],
                [$versionId, $weaponIds['heroshooter_replica'], $groupIds['C-']],
                [$versionId, $weaponIds['l3reelgun'], $groupIds['C-']],
                [$versionId, $weaponIds['maneuver'], $groupIds['C-']],
                [$versionId, $weaponIds['momiji'], $groupIds['C-']],
                [$versionId, $weaponIds['nzap85'], $groupIds['C-']],
                [$versionId, $weaponIds['promodeler_mg'], $groupIds['C-']],
                [$versionId, $weaponIds['promodeler_rg'], $groupIds['C-']],
                [$versionId, $weaponIds['quadhopper_black'], $groupIds['C-']],
                [$versionId, $weaponIds['sharp'], $groupIds['C-']],
                [$versionId, $weaponIds['sputtery'], $groupIds['C-']],
                [$versionId, $weaponIds['sputtery_hue'], $groupIds['C-']],
                [$versionId, $weaponIds['sshooter'], $groupIds['C-']],
                [$versionId, $weaponIds['sshooter_collabo'], $groupIds['C-']],
                [$versionId, $weaponIds['wakaba'], $groupIds['C-']],

                [$versionId, $weaponIds['explosher'], $groupIds['D+']],
                [$versionId, $weaponIds['furo'], $groupIds['D+']],
                [$versionId, $weaponIds['longblaster'], $groupIds['D+']],
                [$versionId, $weaponIds['rapid'], $groupIds['D+']],
                [$versionId, $weaponIds['rapid_elite'], $groupIds['D+']],

                [$versionId, $weaponIds['bucketslosher'], $groupIds['D-']],
                [$versionId, $weaponIds['bucketslosher_deco'], $groupIds['D-']],
                [$versionId, $weaponIds['clashblaster'], $groupIds['D-']],
                [$versionId, $weaponIds['hissen'], $groupIds['D-']],
                [$versionId, $weaponIds['hotblaster'], $groupIds['D-']],
                [$versionId, $weaponIds['nova'], $groupIds['D-']],
                [$versionId, $weaponIds['nova_neo'], $groupIds['D-']],
                [$versionId, $weaponIds['screwslosher'], $groupIds['D-']],

                [$versionId, $weaponIds['campingshelter'], $groupIds['E+']],
                [$versionId, $weaponIds['dynamo'], $groupIds['E+']],
                [$versionId, $weaponIds['jimuwiper'], $groupIds['E+']],
                [$versionId, $weaponIds['variableroller'], $groupIds['E+']],
                [$versionId, $weaponIds['wideroller'], $groupIds['E+']],

                [$versionId, $weaponIds['carbon'], $groupIds['E-']],
                [$versionId, $weaponIds['carbon_deco'], $groupIds['E-']],
                [$versionId, $weaponIds['drivewiper'], $groupIds['E-']],
                [$versionId, $weaponIds['hokusai'], $groupIds['E-']],
                [$versionId, $weaponIds['pablo'], $groupIds['E-']],
                [$versionId, $weaponIds['pablo_hue'], $groupIds['E-']],
                [$versionId, $weaponIds['parashelter'], $groupIds['E-']],
                [$versionId, $weaponIds['splatroller'], $groupIds['E-']],
                [$versionId, $weaponIds['spygadget'], $groupIds['E-']],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%x_matching_group_weapon3}}',
            '{{%x_matching_group3}}',
            '{{%x_matching_group_version3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%x_matching_group_version3}}',
            '{{%x_matching_group3}}',
            '{{%x_matching_group_weapon3}}',
        ];
    }
}
