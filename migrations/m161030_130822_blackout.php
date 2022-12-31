<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m161030_130822_blackout extends Migration
{
    public function up()
    {
        $this->execute(
            \Yii::$app->db
                ->createCommand(
                    'CREATE TYPE blackout_type AS ENUM ( :v1, :v2, :v3, :v4 )',
                    [
                        ':v1' => 'no',              // 黒塗りしない
                        ':v2' => 'not-private',     // プラベでは黒塗りしない（その他は黒塗り）
                        ':v3' => 'not-friend',      // タッグの味方は黒塗りしない（プラベもしない、その他は黒塗り）
                        ':v4' => 'always',          // 自分以外全員黒塗り
                    ],
                )
                ->rawSql,
        );
        $this->execute("ALTER TABLE {{user}} ADD COLUMN [[blackout]] blackout_type NOT NULL DEFAULT 'no'");
        $this->update(
            'user',
            ['blackout' => 'always'],
            ['is_black_out_others' => true],
        );
        $this->execute('ALTER TABLE {{user}} DROP COLUMN [[is_black_out_others]]');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user}} ADD COLUMN [[is_black_out_others]] BOOLEAN NOT NULL DEFAULT FALSE');
        $this->update(
            'user',
            ['is_black_out_others' => true],
            ['<>', 'blackout', 'no'],
        );
        $this->execute('ALTER TABLE {{user}} DROP COLUMN [[blackout]]');
        $this->execute('DROP TYPE blackout_type');
    }
}
