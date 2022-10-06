<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m221004_112533_salmon_weapon3 extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_random3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull(),
            'name' => $this->string(63)->notNull(),
        ]);

        $this->createTable('{{%salmon_weapon3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull(),
            'name' => $this->string(63)->notNull(),
            'weapon_id' => $this->pkRef('{{%weapon3}}')->unique()->null(),
        ]);

        $this->createTable('{{%salmon_weapon3_alias}}', [
            'id' => $this->primaryKey(),
            'weapon_id' => $this->pkRef('{{%salmon_weapon3}}')->notNull(),
            'key' => $this->apiKey3()->notNull(),
        ]);

        $this->batchInsert('{{%salmon_random3}}', ['key', 'name'], [
            ['random', 'Random'],
            ['random_rare', 'Random (Rare)'],
        ]);

        $db = $this->db;
        assert($db instanceof Connection);

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName('{{%salmon_weapon3}}'),
            implode(', ', array_map(
                fn (string $columnName): string => $db->quoteColumnName($columnName),
                ['key', 'name', 'weapon_id']
            )),
            (new Query())
                ->select(['key', 'name', 'id'])
                ->from('{{%weapon3}}')
                ->where('{{%weapon3}}.[[id]] = {{%weapon3}}.[[canonical_id]]')
                ->orderBy(['id' => SORT_ASC])
                ->createCommand($db)
                ->rawSql,
        ]);
        $this->execute($sql);

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName('{{%salmon_weapon3_alias}}'),
            implode(', ', array_map(
                fn (string $columnName): string => $db->quoteColumnName($columnName),
                ['weapon_id', 'key']
            )),
            (new Query())
                ->select([
                    'weapon_id' => '{{%salmon_weapon3}}.[[id]]',
                    'key' => '{{%weapon3_alias}}.[[key]]',
                ])
                ->from('{{%salmon_weapon3}}')
                ->innerJoin('{{%weapon3}}', '{{%salmon_weapon3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
                ->innerJoin('{{%weapon3_alias}}', '{{%weapon3}}.[[id]] = {{%weapon3_alias}}.[[weapon_id]]')
                ->orderBy([
                    '{{%weapon3_alias}}.[[weapon_id]]' => SORT_ASC,
                    '{{%weapon3_alias}}.[[key]]' => SORT_ASC,
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $this->execute($sql);

        $this->insert('{{%salmon_weapon3}}', [
            'key' => 'kuma_stringer',
            'name' => 'Grizzco Stringer',
            'weapon_id' => null,
        ]);

        $this->insert('{{%salmon_weapon3_alias}}', [
            'weapon_id' => $this->key2id('{{%salmon_weapon3}}', 'kuma_stringer'),
            'key' => self::name2key3('Grizzco Stringer'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon_weapon3_alias}}',
            '{{%salmon_weapon3}}',
            '{{%salmon_random3}}',
        ]);

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%salmon_random3}}',
            '{{%salmon_weapon3}}',
            '{{%salmon_weapon3_alias}}',
        ];
    }
}
