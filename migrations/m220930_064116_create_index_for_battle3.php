<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m220930_064116_create_index_for_battle3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        foreach ($this->getData() as $indexName => $columns) {
            $this->execute($this->buildQueryForCreate($indexName, $columns));
        }

        $this->doVacuumTables();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        foreach (array_keys($this->getData()) as $indexName) {
            $this->dropIndex($indexName, '{{%battle3}}');
        }

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%battle3}}',
        ];
    }

    public function getData(): array
    {
        return [
            'battle3_user_id_filter_key' => ['user_id', 'end_at DESC', 'id DESC'],
            'battle3_lobby_id_filter_key' => ['user_id', 'lobby_id', 'end_at DESC', 'id DESC'],
            'battle3_rule_id_filter_key' => ['user_id', 'rule_id', 'end_at DESC', 'id DESC'],
            'battle3_map_id_filter_key' => ['user_id', 'map_id', 'end_at DESC', 'id DESC'],
            'battle3_weapon_id_filter_key' => ['user_id', 'weapon_id', 'end_at DESC', 'id DESC'],
        ];
    }

    private function buildQueryForCreate(string $name, array $list): string
    {
        $db = $this->db;
        assert($db instanceof Connection);

        return vsprintf('CREATE INDEX CONCURRENTLY %s ON %s ( %s ) WHERE %s', [
            $db->quoteColumnName($name),
            $db->quoteTableName('{{%battle3}}'),
            implode(', ', $list),
            implode(' AND ', [
                '{{%battle3}}.[[is_deleted]] = FALSE',
            ]),
        ]);
    }
}
