<?php
use yii\db\Migration;

class m150803_090000_init extends Migration
{
    public function up()
    {
        // INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
        $typePK = $this->db->getQueryBuilder()->typeMap['pk'];

        $tables = [
            'color' => [
                '[[id]] INTEGER NOT NULL PRIMARY KEY',
                '[[name]] TEXT NOT NULL',
                '[[leader]] TEXT NOT NULL',
            ],
            'fest' => [
                '[[id]] INTEGER NOT NULL PRIMARY KEY',
                '[[name]] TEXT NOT NULL',
                '[[start_at]] INTEGER NOT NULL',
                '[[end_at]] INTEGER NOT NULL',
            ],
            'team' => [
                '[[fest_id]] INTEGER NOT NULL REFERENCES {{fest}} ( [[id]] )',
                '[[color_id]] INTEGER NOT NULL REFERENCES {{color}} ( [[id]] )',
                '[[name]] TEXT NOT NULL',
                '[[keyword]] TEXT NOT NULL',
                'PRIMARY KEY ( [[fest_id]], [[color_id]] )',
            ],
            'official_data' => [
                '[[id]] ' . $typePK,
                '[[fest_id]] INTEGER NOT NULL REFERENCES {{fest}} ( [[id]] )',
                '[[sha256sum]] CHAR(44) NOT NULL',
                '[[downloaded_at]] INTEGER NOT NULL',
            ],
            'official_win_data' => [
                '[[data_id]] INTEGER NOT NULL REFERENCES {{official_data}} ( [[id]] )',
                '[[color_id]] INTEGER NOT NULL REFERENCES {{color}} ( [[id]] )',
                '[[count]] INTEGER NOT NULL',
                'PRIMARY KEY ( [[data_id]], [[color_id]] )',
            ],
        ];
        foreach ($tables as $table => $columns) {
            $sql = "CREATE TABLE [[{$table}]] ( ". implode(', ', $columns) . " )";
            $this->execute($sql);
        }
        return true;
    }

    public function down()
    {
        $tables = [
            'official_win_data',
            'official_data',
            'team',
            'fest',
            'color',
        ];
        foreach ($tables as $table) {
            $this->dropTable($table);
        }
    }
}
