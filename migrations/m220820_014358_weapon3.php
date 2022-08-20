<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

final class m220820_014358_weapon3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%mainweapon3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3(),
            'type_id' => $this->pkRef('{{%weapon_type3}}')->notNull(),
            'name' => $this->string(48)->notNull()->unique(),
        ]);

        $this->createTable('{{%weapon3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3(),
            'mainweapon_id' => $this->pkRef('{{%mainweapon3}}')->notNull(),
            'subweapon_id' => $this->pkRef('{{%subweapon3}}')->null(), // temporary until launch
            'special_id' => $this->pkRef('{{%special3}}')->null(), // temporary until launch
            'canonical_id' => $this->pkRef('{{%weapon3}}')
                ->defaultValue(new Expression("currval('weapon3_id_seq'::regclass)"))
                ->notNull(),
            'name' => $this->string(48)->notNull()->unique(),
        ]);

        $this->createTable('{{%weapon3_alias}}', [
            'id' => $this->primaryKey(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
            'key' => $this->apiKey3(),
            'UNIQUE ([[weapon_id]], [[key]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%weapon3_alias}}',
            '{{%weapon3}}',
            '{{%mainweapon3}}',
        ]);

        return true;
    }
}
