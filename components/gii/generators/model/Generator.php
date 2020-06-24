<?php

/**
 * @copyright Copyright (C) 2008 Yii Software LLC
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\gii\generators\model;

use Yii;
use yii\db\Schema;
use yii\gii\generators\model\Generator as BaseGenerator;

class Generator extends BaseGenerator
{
    private const RULE_LIMIT = 120 - 12;

    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }

            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }

            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $types['integer'][] = $column->name;
                    break;

                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;

                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;

                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                    $types['safe'][] = $column->name;
                    break;

                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        $rules = [];
        $driverName = $this->getDbDriverName();
        foreach ($types as $type => $columns) {
            if ($driverName === 'pgsql' && $type === 'integer') {
                $offset = 0;
                $limit = count($columns);
                while ($offset < count($columns)) {
                    $tmpColumns = array_slice($columns, $offset, $limit);
                    $rule = vsprintf("[[%s], 'default', 'value' => null],", [
                        implode(', ', array_map(
                            fn ($_) => "'" . addslashes($_) . "'",
                            $tmpColumns
                        )),
                    ]);
                    if (strlen($rule) > static::RULE_LIMIT) {
                        --$limit;
                    } else {
                        $rules[] = $rule;
                        $offset += $limit;
                        $limit = count($columns) - $offset;
                    }
                }
            }

            $offset = 0;
            $limit = count($columns);
            while ($offset < count($columns)) {
                $tmpColumns = array_slice($columns, $offset, $limit);
                $rule = vsprintf("[[%s], '%s'],", [
                    implode(', ', array_map(
                        fn ($_) => "'" . addslashes($_) . "'",
                        $tmpColumns
                    )),
                    addslashes($type),
                ]);
                if ($limit > 0 && strlen($rule) > static::RULE_LIMIT) {
                    --$limit;
                } else {
                    $rules[] = $rule;
                    $offset += $limit;
                    $limit = count($columns) - $offset;
                }
            }
        }
        foreach ($lengths as $length => $columns) {
            $offset = 0;
            $limit = count($columns);
            while ($offset < count($columns)) {
                $tmpColumns = array_slice($columns, $offset, $limit);
                $rule = vsprintf("[[%s], 'string', 'max' => %d],", [
                    implode(', ', array_map(
                        fn ($_) => "'" . addslashes($_) . "'",
                        $tmpColumns
                    )),
                    addslashes($type),
                ]);
                if ($limit > 0 && strlen($rule) > static::RULE_LIMIT) {
                    --$limit;
                } else {
                    $rules[] = $rule;
                    $offset += $limit;
                    $limit = count($columns) - $offset;
                }
            }
        }

        $db = $this->getDbConnection();

        // Unique indexes rules
        try {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($table), [$table->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique'],";
                    } elseif ($attributesCount > 1) {
                        $columnsList = implode("', '", $uniqueColumns);
                        $rule = "[['$columnsList'], 'unique', 'targetAttribute' => ['$columnsList']],";
                        if (strlen($rule) <= static::RULE_LIMIT) {
                            $rules[] = $rule;
                        } else {
                            $tmp = [];
                            $tmp[] = '[';
                            $rule = "['{$columnsList}'],";
                            if (strlen($rule) <= static::RULE_LIMIT - 4) {
                                $tmp[] = '    ' . $rule;
                            } else {
                                $tmp[] = '    [';
                                foreach ($uniqueColumns as $_) {
                                    $tmp[] = "        '" . $_ . "',";
                                }
                                $tmp[] = '    ],';
                            }
                            $tmp[] = "    'unique',";
                            $tmp[] = "    'targetAttribute' => [";
                            foreach ($uniqueColumns as $_) {
                                $tmp[] = "        '" . $_ . "',";
                            }
                            $tmp[] = '    ],';
                            $tmp[] = '],';
                            $rules[] = implode("\n", $tmp);
                        }
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);
            $attributes = implode("', '", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "'$key' => '$value'";
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $rules[] = "[['$attributes'], 'exist',";
            $rules[] = "    'skipOnError' => true,";
            $rules[] = "    'targetClass' => $refClassName::class,";
            $rules[] = "    'targetAttribute' => [";
            foreach ($refs as $key => $value) {
                $rules[] = "        '{$key}' => '{$value}',";
            }
            $rules[] = "    ],";
            $rules[] = "],";
        }

        return $rules;
    }
}
