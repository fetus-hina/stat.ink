<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m191112_153426_remove_duplicated_salmon_stats extends Migration
{
    public function up()
    {
        Yii::$app->db->transaction(function () {
            $this->execute(vsprintf('DELETE FROM %s WHERE (NOT (%s IN (%s)))', [
                '{{salmon_stats2}}',
                '[[id]]',
                vsprintf('SELECT %s FROM %s GROUP BY %s', [
                    'MIN([[id]])',
                    '{{salmon_stats2}}',
                    implode(', ', array_map(
                        fn (string $column): string => "[[{$column}]]",
                        [
                            'user_id',
                            'work_count',
                            'total_golden_eggs',
                            'total_eggs',
                            'total_rescued',
                            'total_point',
                        ],
                    )),
                ]),
            ]));
        });

        $this->execute('VACUUM FULL {{salmon_stats2}}');
    }

    public function down()
    {
    }
}
