<?php
/**
 * This view is used by console/controllers/MigrateController.php
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */
echo "<?php\n";
echo "/**\n";
echo " * @copyright Copyright (C) 2015-" . date('Y', time()) . " AIZAWA Hina\n";
echo " * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT\n";
echo " * @author AIZAWA Hina <hina@bouhime.com>\n";
echo " */\n";

if (!empty($namespace)) {
    echo "\n";
    echo "namespace {$namespace};\n";
}
?>

use app\components\db\Migration;

class <?= $className ?> extends Migration
{
    public function up()
    {

    }

    public function down()
    {
        echo "<?= $className ?> cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
