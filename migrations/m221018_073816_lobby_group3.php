<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m221018_073816_lobby_group3 extends Migration
{
    protected function vacuumTables(): array
    {
        return [
            '{{%lobby3}}',
            '{{%lobby_group3}}',
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upTable();
        $this->alterLobby();

        return true;
    }

    private function upTable(): void
    {
        $this->createTable('{{%lobby_group3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull(),
            'name' => $this->string(32)->notNull(),
            'rank' => $this->integer()->notNull()->unique(),
            'importance' => $this->integer()->notNull()->unique(),
        ]);

        $this->batchInsert('{{%lobby_group3}}', ['key', 'name', 'rank', 'importance'], [
            ['regular', 'Regular Battle', 100, 400],
            ['bankara', 'Anarchy Battle', 200, 500],
            ['splatfest', 'Splatfest', 700, 300],
            ['private', 'Private Battle', 900, 100],
        ]);
    }

    private function alterLobby(): void
    {
        $idMap = ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('{{%lobby_group3}}')->all(),
            'key',
            'id',
        );

        $updateMap = [
            'bankara_challenge' => (int)$idMap['bankara'],
            'bankara_open' => (int)$idMap['bankara'],
            'private' => (int)$idMap['private'],
            'regular' => (int)$idMap['regular'],
            'splatfest_challenge' => (int)$idMap['splatfest'],
            'splatfest_open' => (int)$idMap['splatfest'],
        ];
        unset($idMap);

        $this->addColumn(
            '{{%lobby3}}',
            'group_id',
            (string)$this->pkRef('{{%lobby_group3}}')->null(),
        );

        $db = $this->db;
        assert($db instanceof Connection);

        $sql = vsprintf('UPDATE %s SET [[group_id]] = %s', [
            $db->quoteTableName('{{%lobby3}}'),
            vsprintf('(CASE %s %s END)', [
                $db->quoteColumnName('key'),
                implode(' ', array_map(
                    fn (string $key, $id): string => vsprintf('WHEN %s THEN %d', [
                        $db->quoteValue($key),
                        (int)$id,
                    ]),
                    array_keys($updateMap),
                    array_values($updateMap),
                )),
            ]),
        ]);
        $this->execute($sql);

        $this->alterColumn(
            '{{%lobby3}}',
            'group_id',
            (string)$this->integer()->notNull(),
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lobby3}}', 'group_id');
        $this->dropTable('{{%lobby_group3}}');

        return true;
    }
}
