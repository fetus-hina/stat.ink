<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m190612_182430_weapon_tier2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('stat_weapon2_tier', [
            'id' => $this->primaryKey(),
            'version_group_id' => $this->pkRef('splatoon_version_group2')->notNull(),
            'month' => $this->date()->notNull()->check('EXTRACT(day FROM month) = 1'),
            'rule_id' => $this->pkRef('rule2')->notNull(),
            'weapon_id' => $this->pkRef('weapon2')->notNull(),
            'players_count' => $this->bigInteger()->notNull(),
            'win_count' => $this->bigInteger()->notNull(),
            'win_percent' => $this->float()->notNull(),
            'avg_kill' => $this->float()->notNull(),
            'med_kill' => $this->float()->notNull(),
            'stderr_kill' => $this->float()->notNull(),
            'stddev_kill' => $this->float()->notNull(),
            'avg_death' => $this->float()->notNull(),
            'med_death' => $this->float()->notNull(),
            'stderr_death' => $this->float()->notNull(),
            'stddev_death' => $this->float()->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
            'UNIQUE ([[rule_id]], [[month]], [[version_group_id]], [[weapon_id]])',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('stat_weapon2_tier');
    }
}
