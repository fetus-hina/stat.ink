<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\Special;
use app\models\Weapon;

class m150929_064010_special extends Migration
{
    public function up()
    {
        $this->createTable('special', [
            'id' => $this->primaryKey(),
            'key' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('special', ['key', 'name'], [
            [ 'barrier', 'Bubbler' ],
            [ 'bombrush', 'Bomb Rush' ],
            [ 'daioika', 'Kraken' ],
            [ 'megaphone', 'Killer Wail' ],
            [ 'supersensor', 'Echolocator' ],
            [ 'supershot', 'Inkzooka' ],
            [ 'tornado', 'Inkstrike' ],
        ]);

        $this->execute('ALTER TABLE {{weapon}} ADD COLUMN [[special_id]] INTEGER');
        foreach ($this->makeUpdate() as $tmp) {
            list($weaponId, $specialId) = $tmp;
            $this->update(
                'weapon',
                ['special_id' => $specialId],
                'id = :weapon_id',
                ['weapon_id' => $weaponId],
            );
        }
        $this->addForeignKey('fk_weapon_2', 'weapon', 'special_id', 'special', 'id', 'RESTRICT');
        $this->execute('ALTER TABLE {{weapon}} ALTER COLUMN [[special_id]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{weapon}} DROP COLUMN [[special_id]]');
        $this->dropTable('special');
    }

    public function makeUpdate()
    {
        $currentSpecialId = null;
        $fh = fopen(__FILE__, 'rt');
        fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
        while (!feof($fh)) {
            $line = rtrim(fgets($fh));
            if ($line != '') {
                if (substr($line, 0, 1) !== ' ') {
                    $currentSpecialId = Special::findOne(['key' => $line])->id;
                } else {
                    $line = trim($line);
                    $weaponId = Weapon::findOne(['key' => $line])->id;
                    yield [$weaponId, $currentSpecialId];
                }
            }
        }
        fclose($fh);
    }
}

// phpcs:disable

__halt_compiler();
barrier
    hissen
    hotblaster_custom
    pablo_hue
    rapid
    squiclean_a
    wakaba

bombrush
    herocharger_replica
    heroshooter_replica
    rapid_deco
    sharp
    splatcharger
    splatscope
    sshooter

daioika
    96gal_deco
    hokusai
    jetsweeper_custom
    l3reelgun_d
    liter3k_custom
    splatroller_collabo

megaphone
    52gal
    bamboo14mk1
    bold
    dualsweeper_custom
    heroroller_replica
    hotblaster
    l3reelgun
    splatcharger_wakame
    splatroller
    splatscope_wakame

supersensor
    96gal
    dualsweeper
    dynamo
    h3reelgun
    liter3k
    liter3k_scope
    momiji
    nzap85

supershot
    carbon
    nova
    octoshooter_replica
    prime_collabo
    promodeler_mg
    sharp_neo
    splatspinner
    squiclean_b
    sshooter_collabo

tornado
    52gal_deco
    barrelspinner
    bucketslosher
    dynamo_tesla
    jetsweeper
    longblaster
    nzap89
    pablo
    prime
    promodeler_rg
