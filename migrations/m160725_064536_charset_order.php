<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Charset;
use yii\db\Migration;

class m160725_064536_charset_order extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{charset}} ' . implode(', ', [
            'ADD COLUMN [[is_unicode]] BOOLEAN NOT NULL DEFAULT FALSE',
            'ADD COLUMN [[order]] INTEGER',
        ]));

        $order = [
            'UTF-8'     =>  1,
            'UTF-16LE'  =>  2,
            'CP1252'    => 11,
            'CP932'     => 12,
        ];
        foreach (Charset::find()->asArray()->all() as $charset) {
            $name = $charset['php_name'];
            $this->update(
                'charset',
                [
                    'is_unicode' => substr($name, 0, 4) === 'UTF-',
                    'order' => $order[$name],
                ],
                [
                    'id' => $charset['id'],
                ],
            );
        }

        $this->execute('ALTER TABLE {{charset}} ALTER COLUMN [[order]] SET NOT NULL');
        $this->createIndex('ix_charset_order', 'charset', 'order', true);
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{charset}} DROP COLUMN [[is_unicode]], DROP COLUMN [[order]]');
    }
}
