<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\db;

use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ColumnSchemaBuilder;
use yii\db\Connection;
use yii\db\Migration as BaseMigration;
use yii\db\Query;
use yii\db\Schema;

use function array_keys;
use function array_map;
use function assert;
use function filter_var;
use function implode;
use function is_int;
use function is_string;
use function preg_split;
use function sprintf;
use function vsprintf;

use const FILTER_VALIDATE_INT;
use const PREG_SPLIT_NO_EMPTY;

class Migration extends BaseMigration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->beforeUp();
        $result = parent::up();
        if ($result !== false) {
            $this->doVacuumTables();
            $this->afterUp();
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @return string[]
     */
    protected function vacuumTables(): array
    {
        return [];
    }

    protected function doVacuumTables(): void
    {
        if (!$tables = $this->vacuumTables()) {
            return;
        }

        $db = $this->db;
        assert($db instanceof Connection);

        foreach ($tables as $table) {
            $time = $this->beginCommand(sprintf('vacuum table %s', $table));
            $sql = sprintf('VACUUM ( ANALYZE ) %s', $db->quoteTableName($table));
            $this->db->createCommand($sql)->execute();
            $this->endCommand($time);
        }
    }

    /**
     * @return ($nullable is true ? int|null : int)
     */
    public function key2id(
        string $tableName,
        string $key,
        string $keyColumn = 'key',
        bool $nullable = false,
    ): ?int {
        $value = filter_var(
            (new Query())
                ->select(['id'])
                ->from($tableName)
                ->where([$keyColumn => $key])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );

        if (!is_int($value)) {
            return $nullable
                ? null
                : throw new InvalidArgumentException("The key $key is not exists in $tableName");
        }

        return $value;
    }

    public function apiKey(int $length = 16): ColumnSchemaBuilder
    {
        return $this->string($length)->notNull()->unique();
    }

    public function apiKey3(string $columnName = 'key', int $length = 32): ColumnSchemaBuilder
    {
        return $this->apiKey($length)
            ->check(vsprintf('%s ~ %s', [
                $this->getDb()->quoteColumnName($columnName),
                $this->getDb()->quoteValue('^[0-9a-z_]+$'),
            ]));
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
            'REFERENCES %s(%s)',
            $this->getDb()->quoteTableName($table),
            $this->getDb()->quoteColumnName($column),
        ));
    }

    public function bigPkRef(string $table, string $column = 'id')
    {
        return $this->bigInteger()->notNull()->append(sprintf(
            'REFERENCES %s(%s)',
            $this->getDb()->quoteTableName($table),
            $this->getDb()->quoteColumnName($column),
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
                (array)$columns,
            )),
        );
    }

    public function addColumns(string $table, array $columns): void
    {
        $time = $this->beginCommand(sprintf(
            'add columns %s to table %s',
            implode(', ', array_keys($columns)),
            $table,
        ));

        $db = $this->db;
        $alter = [];
        $comments = [];
        foreach ($columns as $column => $type) {
            $alter[] = sprintf(
                'ADD COLUMN %s %s',
                $db->quoteColumnName($column),
                $db->getQueryBuilder()->getColumnType($type),
            );

            if ($type instanceof ColumnSchemaBuilder && $type->comment !== null) {
                $comments[] = $db->getQueryBuilder()->addCommentOnColumn($table, $column, $type->comment);
            }
        }

        if (!empty($alter)) {
            $sql = 'ALTER TABLE ' . $db->quoteTableName($table) . ' ' . implode(', ', $alter);
            $db->createCommand($sql)->execute();
        }

        foreach ($comments as $comment) {
            $db->createCommand($comment)->execute();
        }

        if (!empty($alter) || !empty($comments)) {
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

    /**
     * @param string[] $columns
     */
    public function dropColumns(string $table, array $columns): void
    {
        $time = $this->beginCommand(sprintf(
            'drop columns %s from table %s',
            implode(', ', array_values($columns)),
            $table,
        ));

        $db = $this->db;
        $sql = vsprintf('ALTER TABLE %s %s', [
            $db->quoteTableName($table),
            implode(
                ', ',
                array_map(
                    fn (string $column): string => vsprintf('DROP COLUMN %s', [
                        $db->quoteColumnName($column),
                    ]),
                    $columns,
                ),
            ),
        ]);
        $db->createCommand($sql)->execute();
        $this->endCommand($time);
    }

    /**
     * @param string|string[] $table
     */
    public function analyze(string|array $table): void
    {
        // PgSQL 11 以降(?) なら複数テーブルを指定できるはずだが、分岐がめんどくさいので
        // とりあえずループで回す
        if (is_array($table)) {
            foreach ($table as $t) {
                $this->analyze($t);
            }

            return;
        }

        $db = $this->db;
        $this->execute(
            vsprintf('VACUUM ( ANALYZE ) %s', [
                $db->quoteTableName($table),
            ]),
        );
    }

    public function isTableExists(string $table, string $schema = 'public'): bool
    {
        $db = Yii::$app->db;
        $sql = sprintf(
            'SELECT to_regclass(%s)',
            $db->quoteValue(sprintf(
                '%s.%s',
                $db->quoteTableName($schema),
                $db->quoteTableName($table),
            )),
        );
        $result = $db->createCommand($sql)->queryScalar();
        return $result != '';
    }
}
