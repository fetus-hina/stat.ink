<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\helpers\GitAuthorHelper;
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

$now = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Tokyo')))
    ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());

echo "<?php\n";
echo "\n";
?>
/**
 * @copyright Copyright (C) <?= $now->format('Y') ?> AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
<?php foreach (array_keys(GitAuthorHelper::getAuthors()) as $author): ?>
 * @author <?= $author . "\n" ?>
<?php endforeach; ?>
 */

declare(strict_types=1);

namespace <?= $generator->ns ?>;

use Yii;
use yii\db\ActiveQuery;
use <?= ltrim($generator->baseClass, '\\') ?>;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
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

    public function rules()
    {
        return [<?= "\n            " . preg_replace('/::className\(\)/', '::class', implode(",\n            ", $rules)) . ",\n        " ?>];
    }

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
