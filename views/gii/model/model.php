<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

use app\components\helpers\GitHelper;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\generators\model\Generator;
use yii\web\View;

/**
 * @var Generator $generator
 * @var TableSchema $tableSchema
 * @var View $this
 * @var array<string, array> $relations array list of relations (name => relation declaration)
 * @var array<string, string> $labels list of attribute labels
 * @var string $className class name
 * @var string $queryClassName query class name
 * @var string $tableName full table name
 * @var string[] $rules list of validation rules
 */

$path = Yii::getAlias('@app/models/' . $className . '.php');

// The type accepted on assignment, when it differs from the type read from the DB
$writeType = fn (ColumnSchema $column): ?string => match ($column->type) {
  Schema::TYPE_DECIMAL => 'float|string',
  Schema::TYPE_DATE,
  Schema::TYPE_DATETIME,
  Schema::TYPE_TIME,
  Schema::TYPE_TIMESTAMP => 'string|ExpressionInterface',
  default => null,
};
$useExpressionInterface = array_any(
  $tableSchema->columns,
  fn (ColumnSchema $column): bool => str_contains((string)$writeType($column), 'ExpressionInterface'),
);

echo "<?php\n";
echo "\n";
?>
/**
 * @copyright Copyright (C) <?= GitHelper::getCopyrightYear($path) ?> AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace <?= $generator->ns ?>;

use Override;
use Yii;
use yii\db\ActiveQuery;
use <?= ltrim($generator->baseClass, '\\') ?>;
<?php if ($useExpressionInterface): ?>
use yii\db\ExpressionInterface;
<?php endif; ?>

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
<?php if (($columnWriteType = $writeType($column)) === null): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php else: ?>
 * @property-read <?= "{$column->phpType} \${$column->name}\n" ?>
 * @property-write <?= "{$columnWriteType} \${$column->name}\n" ?>
<?php endif; ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= preg_replace('!^.+\x5c([^\x5c]+)!', '$1', $generator->baseClass) . "\n" ?>
{
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    #[Override]
    public function rules()
    {
        return [<?= "\n            " . preg_replace('/::className\(\)/', '::class', implode(",\n            ", $rules)) . ",\n        " ?>];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>

    public function get<?= $name ?>(): ActiveQuery
    {
        <?= preg_replace('/::className\(\)/', '::class', $relation[0]) . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
