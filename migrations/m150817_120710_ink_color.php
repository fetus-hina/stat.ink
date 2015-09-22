<?php
use yii\db\Migration;

class m150817_120710_ink_color extends Migration
{
    public function up()
    {
        $this->addColumn('team', 'ink_color', $this->string(6));

        $colors = [
            1 => [ null, null ],
            2 => [ 'd9435f', '5cb85c' ], // 349, 69, 85  :  120, 50, 72
            3 => [ 'cc8829', 'd9bb82' ],
            4 => [ '5bcc3d', 'bf60b8' ], 
        ];
        foreach ($colors as $festId => $colorCodes) {
            foreach ($colorCodes as $i => $colorCode) {
                $this->update(
                    'team',
                    ['ink_color' => $colorCode],
                    ['fest_id' => $festId, 'color_id' => $i + 1 ]
                );
            }
        }
    }

    public function down()
    {
        // $this->dropColumn('team', 'ink_color');
        // がしたいだけなのだけど、SQLite がサポートしていないので
        // 頑張って全部書く

        // Yii 2.0.6 現在、RENAME TABLE 作って死ぬので使えない
        // See: https://github.com/yiisoft/yii2/issues/9442
        // $this->renameTable('team', 'team_old');

        $this->execute('ALTER TABLE {{team}} RENAME TO {{team_old}}');

        $this->execute(sprintf(
            'CREATE TABLE {{team}} ( %s )',
            implode(', ', [
                '[[fest_id]] INTEGER NOT NULL REFERENCES {{fest}} ( [[id]] )',
                '[[color_id]] INTEGER NOT NULL REFERENCES {{color}} ( [[id]] )',
                '[[name]] TEXT NOT NULL',
                '[[keyword]] TEXT NOT NULL',
                'PRIMARY KEY ( [[fest_id]], [[color_id]] )',
            ])
        ));

        $this->execute(
            'INSERT INTO {{team}} ' .
            'SELECT [[fest_id]], [[color_id]], [[name]], [[keyword]] ' .
            'FROM {{team_old}} ' .
            'ORDER BY {{team_old}}.[[fest_id]], {{team_old}}.[[color_id]]'
        );
        $this->dropTable('team_old');
    }
}
