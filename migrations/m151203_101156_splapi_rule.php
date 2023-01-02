<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Rule;
use yii\db\Migration;

class m151203_101156_splapi_rule extends Migration
{
    public function up()
    {
        $rule = []; // [ 'arowana' => 42, ... ]
        foreach (Rule::find()->all() as $_) {
            $rule[$_->key] = $_->id;
        }

        // 今後表記揺れが発生する可能性がある(ないけど)ので rule_id を PKEY にはしない
        $this->createTable('splapi_rule', [
            'id' => $this->primaryKey(),
            'rule_id' => $this->integer()->notNull(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->addForeignKey('fk_splapi_rule_1', 'splapi_rule', 'rule_id', 'rule', 'id');

        $this->batchInsert(
            'splapi_rule',
            ['rule_id', 'name'],
            [
                [ $rule['nawabari'], 'ナワバリバトル' ],
                [ $rule['area'], 'ガチエリア' ],
                [ $rule['yagura'], 'ガチヤグラ' ],
                [ $rule['hoko'], 'ガチホコ' ],
            ],
        );
    }

    public function down()
    {
        $this->dropTable('splapi_rule');
    }
}
