<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170328_132058_weapon_type2 extends Migration
{
    public function up()
    {
        $this->createTable('weapon_type2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'category_id' => $this->pkRef('weapon_category2', 'id'),
            'name' => $this->string(32)->notNull()->unique(),
        ]);

        $c = $this->getCategories();
        $this->batchInsert('weapon_type2', ['category_id', 'key', 'name'], [
            [ $c['shooter'],   'shooter',   'Shooters' ],
            [ $c['shooter'],   'blaster',   'Blasters' ],
            [ $c['roller'],    'roller',    'Rollers' ],
            [ $c['roller'],    'brush',     'Brushes' ],
            [ $c['charger'],   'charger',   'Chargers' ],
            [ $c['splatling'], 'splatling', 'Splatlings' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon_type2');
    }

    private function getCategories()
    {
        return ArrayHelper::map(
            (new Query())
                ->select(['key', 'id'])
                ->from('weapon_category2')
                ->all(),
            'key',
            'id'
        );
    }
}
