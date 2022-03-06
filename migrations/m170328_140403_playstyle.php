<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170328_140403_playstyle extends Migration
{
    public function up()
    {
        $this->upNSMode();
        $this->upControllerMode();
        $this->upPlaystyle();
    }

    protected function upNSMode()
    {
        $this->createTable('ns_mode2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('ns_mode2', ['key', 'name'], [
            [ 'tv',       'TV Mode' ],
            [ 'tabletop', 'Tabletop Mode' ],
            [ 'handheld', 'Handheld Mode' ],
        ]);
    }

    protected function upControllerMode()
    {
        $this->createTable('controller_mode2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('controller_mode2', ['key', 'name'], [
            [ 'procon',                 'Pro Controller' ],
            [ 'joycon_with_grip',       'Joy-Con with Grip' ],
            [ 'joycon_wo_grip',    'Joy-Con without Grip' ],
            [ 'handheld',               'Handheld Mode' ],
        ]);
    }

    protected function upPlaystyle()
    {
        $ns = ArrayHelper::map(
            (new Query())->select(['key', 'id'])->from('ns_mode2')->all(),
            'key',
            'id'
        );
        $ctl = ArrayHelper::map(
            (new Query())->select(['key', 'id'])->from('controller_mode2')->all(),
            'key',
            'id'
        );
        $this->createTable('playstyle2', [
            'ns_mode_id'            => $this->pkRef('ns_mode2'),
            'controller_mode_id'    => $this->pkRef('controller_mode2'),
            'PRIMARY KEY([[ns_mode_id]], [[controller_mode_id]])',
        ]);
        $this->batchInsert('playstyle2', ['ns_mode_id', 'controller_mode_id'], [
            [ $ns['tv'],       $ctl['procon'] ],
            [ $ns['tv'],       $ctl['joycon_with_grip'] ],
            [ $ns['tv'],       $ctl['joycon_wo_grip'] ],
            [ $ns['tabletop'], $ctl['procon'] ],
            [ $ns['tabletop'], $ctl['joycon_with_grip'] ],
            [ $ns['tabletop'], $ctl['joycon_wo_grip'] ],
            [ $ns['handheld'], $ctl['handheld'] ],
        ]);
    }

    public function down()
    {
        $this->dropTable('playstyle2');
        $this->dropTable('controller_mode2');
        $this->dropTable('ns_mode2');
    }
}
