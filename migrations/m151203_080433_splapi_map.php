<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\Map;

class m151203_080433_splapi_map extends Migration
{
    public function up()
    {
        $map = []; // [ 'arowana' => 42, ... ]
        foreach (Map::find()->all() as $_) {
            $map[$_->key] = $_->id;
        };

        // 今後表記揺れが発生する可能性があるので map_id を PKEY にはしない
        $this->createTable('splapi_map', [
            'id'        => $this->primaryKey(),
            'map_id'    => $this->integer()->notNull(),
            'name'      => $this->string(32)->notNull()->unique(),
        ]);
        $this->addForeignKey('fk_splapi_map_1', 'splapi_map', 'map_id', 'map', 'id');

        $this->batchInsert(
            'splapi_map',
            ['map_id', 'name'],
            [
                [ $map['arowana'],  'アロワナモール' ],
                [ $map['bbass'],    'Ｂバスパーク' ],
                [ $map['dekaline'], 'デカライン高架下' ],
                [ $map['hakofugu'], 'ハコフグ倉庫' ],
                [ $map['hirame'],   'ヒラメが丘団地' ],
                [ $map['hokke'],    'ホッケふ頭' ],
                [ $map['kinmedai'], 'キンメダイ美術館' ],
                [ $map['mahimahi'], 'マヒマヒリゾート＆スパ' ], // たぶん...
                [ $map['masaba'],   'マサバ海峡大橋' ],
                [ $map['mongara'],  'モンガラキャンプ場' ],
                [ $map['mozuku'],   'モズク農園' ],
                [ $map['negitoro'], 'ネギトロ炭鉱' ],
                [ $map['shionome'], 'シオノメ油田' ],
                [ $map['tachiuo'],  'タチウオパーキング' ],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('splapi_map');
    }
}
