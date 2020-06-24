<?php

/**
 * This is the template for generating the model class of a specified table.
 *
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

declare(strict_types=1);

use app\components\helpers\GitHelper;

$now = (new DateTimeImmutable('now', new DateTimeZone('Asia/Tokyo')))
    ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());

$authors = array_filter(
    [
        [
            'AIZAWA Hina',
            'hina@fetus.jp',
        ],
        [
            GitHelper::getUserName(),
            GitHelper::getUserEmail(),
        ],
    ],
    fn ($_) => ($_[0] !== null && $_[1] !== null)
);

$authorsFormatted = array_unique(array_map(
    fn ($_) => sprintf('%s <%s>', $_[0], $_[1]),
    $authors
));

$copyrightHolders = array_unique(array_map(fn ($_) => $_[0], $authors));

echo "<?php\n";
?>

/**
 * @copyright Copyright (C) 2015-<?= $now->format('Y') ?> <?= implode(', ', $copyrightHolders) . "\n" ?>
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
<?php foreach ($authorsFormatted as $author): ?>
 * @author <?= $author . "\n" ?>
<?php endforeach ?>
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
        return [
<?php foreach (explode("\n", implode("\n", $rules)) as $line): ?>
            <?= $line . "\n" ?>
<?php endforeach ?>
        ];
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
