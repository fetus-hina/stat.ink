<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\db;

use Yii;
use yii\db\ColumnSchemaBuilder;
use yii\db\Migration as BaseMigration;
use yii\db\Schema;

use const PREG_SPLIT_NO_EMPTY;

abstract class Migration extends BaseMigration
{
    public function up()
    {
        $this->beforeUp();
        $result = parent::up();
        if ($result !== false) {
            $this->afterUp();
        }
        return $result;
    }

    public function down()
    {
        $this->beforeDown();
        $result = parent::down();
        if ($result !== false) {
            $this->afterDown();
        }
        return $result;
    }

    protected function beforeUp()
    {
    }

    protected function afterUp()
    {
    }

    protected function beforeDown()
    {
    }

    protected function afterDown()
    {
    }

    public function apiKey(int $length = 16)
    {
        return $this->string($length)->notNull()->unique();
    }

    public function timestampTZ(int $precision = 0, bool $withTZ = true)
    {
        $type = sprintf('TIMESTAMP(%d) %s TIME ZONE', $precision, $withTZ ? 'WITH' : 'WITHOUT');
        $builder = $this->getDb()->getSchema()->createColumnSchemaBuilder($type);

        $catMap = $builder->getCategoryMap();
        $catMap[$type] = $catMap[Schema::TYPE_TIMESTAMP];
        $builder->setCategoryMap($catMap);

        return $builder;
    }

    public function pkRef(string $table, string $column = 'id')
    {
        return $this->integer()->notNull()->append(sprintf(
            'REFERENCES {{%s}}([[%s]])',
            $table,
            $column
        ));
    }

    public function bigPkRef(string $table, string $column = 'id')
    {
        return $this->bigInteger()->notNull()->append(sprintf(
            'REFERENCES {{%s}}([[%s]])',
            $table,
            $column
        ));
    }

    public function tablePrimaryKey($columns): string
    {
        if (is_string($columns)) {
            $columns = preg_split('/\s*,\s*/', $columns, -1, PREG_SPLIT_NO_EMPTY);
        }

        return sprintf(
            'PRIMARY KEY ( %s )',
            implode(', ', array_map(
                fn (string $column): string => $this->db->quoteColumnName($column),
                (array)$columns
            ))
        );
    }

    public function addColumns(string $table, array $columns): void
    {
        $time = $this->beginCommand(sprintf(
            'add columns %s to table %s',
            implode(', ', array_keys($columns)),
            $table
        ));

        $db = $this->db;
        $alter = [];
        $comments = [];
        foreach ($columns as $column => $type) {
            $alter[] = sprintf(
                'ADD COLUMN %s %s',
                $db->quoteColumnName($column),
                $db->getQueryBuilder()->getColumnType($type)
            );

            if ($type instanceof ColumnSchemaBuilder && $type->comment !== null) {
                $comments[] = $db->getQueryBuilder()->addCommentOnColumn($table, $column, $type->comment);
            }
        }

        if ($alter) {
            $sql = 'ALTER TABLE ' . $db->quoteTableName($table) . ' ' . implode(', ', $alter);
            $db->createCommand($sql)->execute();
        }

        foreach ($comments as $comment) {
            $db->createCommand($comment)->execute();
        }

        if ($alter || $comments) {
            $db->getSchema()->refreshTableSchema($table);
        }

        $this->endCommand($time);
    }

    public function dropTables(array $tables, bool $ifExists = true): void
    {
        foreach ($tables as $table) {
            if ($ifExists && !$this->isTableExists($table)) {
                continue;
            }
            $this->dropTable($table);
        }
    }

    public function dropColumns(string $table, array $columns): void
    {
        $time = $this->beginCommand(sprintf(
            'drop columns %s from table %s',
            implode(', ', array_keys($columns)),
            $table
        ));

        $db = $this->db;
        $sql = 'ALTER TABLE ' . $db->quoteTableName($table) . ' ' . implode(', ', array_map(
            fn (string $column): string => 'DROP COLUMN ' . $db->quoteColumnName($column),
            $columns
        ));
        $db->createCommand($sql)->execute();
        $this->endCommand($time);
    }

    public function analyze($table): void
    {
        $this->execute("VACUUM ANALYZE {{{$table}}}");
    }

    public function isTableExists(string $table, string $schema = 'public'): bool
    {
        $db = Yii::$app->db;
        $sql = sprintf(
            'SELECT to_regclass(%s)',
            $db->quoteValue(sprintf(
                '%s.%s',
                $db->quoteTableName($schema),
                $db->quoteTableName($table)
            ))
        );
        $result = $db->createCommand($sql)->queryScalar();
        return $result != '';
    }
}
