<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Subweapon;
use app\models\Weapon;
use yii\db\Migration;

class m150928_122509_subweapon extends Migration
{
    public function up()
    {
        $this->createTable('subweapon', [
            'id' => $this->primaryKey(),
            'key' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('subweapon', ['key', 'name'], [
            [ 'chasebomb', 'Seeker' ],
            [ 'jumpbeacon', 'Squid Beakon' ],
            [ 'kyubanbomb', 'Suction Bomb' ],
            [ 'pointsensor', 'Point Sensor' ],
            [ 'poison', 'Disruptor' ],
            [ 'quickbomb', 'Burst Bomb' ],
            [ 'splashbomb', 'Splat Bomb' ],
            [ 'splashshield', 'Splash Wall' ],
            [ 'sprinkler', 'Sprinkler' ],
            [ 'trap', 'Ink Mine' ],
        ]);

        $this->execute('ALTER TABLE {{weapon}} ADD COLUMN [[subweapon_id]] INTEGER');
        foreach ($this->makeUpdate() as $tmp) {
            list($weaponId, $subWeaponId) = $tmp;
            $this->update(
                'weapon',
                ['subweapon_id' => $subWeaponId],
                'id = :weapon_id',
                ['weapon_id' => $weaponId],
            );
        }
        $this->addForeignKey('fk_weapon_1', 'weapon', 'subweapon_id', 'subweapon', 'id', 'RESTRICT');
        $this->execute('ALTER TABLE {{weapon}} ALTER COLUMN [[subweapon_id]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{weapon}} DROP COLUMN [[subweapon_id]]');
        $this->dropTable('subweapon');
    }

    public function makeUpdate()
    {
        $currentSubweaponId = null;
        $fh = fopen(__FILE__, 'rt');
        fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
        while (!feof($fh)) {
            $line = rtrim(fgets($fh));
            if ($line != '') {
                if (substr($line, 0, 1) !== ' ') {
                    $currentSubweaponId = Subweapon::findOne(['key' => $line])->id;
                } else {
                    $line = trim($line);
                    $weaponId = Weapon::findOne(['key' => $line])->id;
                    yield [$weaponId, $currentSubweaponId];
                }
            }
        }
        fclose($fh);
    }
}

// phpcs:disable

__halt_compiler();
chasebomb
    promodeler_mg
    52gal_deco

jumpbeacon
    bold
    dualsweeper_custom
    hokusai
    liter3k_custom
    splatroller_collabo

kyubanbomb
    h3reelgun
    heroroller_replica
    octoshooter_replica
    rapid_deco
    sharp
    splatroller
    splatspinner
    sshooter_collabo

pointsensor
    hotblaster_custom
    prime_collabo
    squiclean_a

poison
    momiji
    l3reelgun
    hotblaster
    hissen

quickbomb
    bucketslosher
    carbon
    heroshooter_replica
    jetsweeper_custom
    l3reelgun_d
    liter3k
    liter3k_scope
    sharp_neo
    sshooter

splashbomb
    dualsweeper
    dynamo_tesla
    herocharger_replica
    nzap85
    prime
    splatcharger
    splatscope
    wakaba

splashshield
    52gal
    96gal_deco
    bamboo14mk1
    barrelspinner
    jetsweeper
    longblaster

sprinkler
    96gal
    dynamo
    nzap89
    pablo
    splatcharger_wakame
    splatscope_wakame

trap
    nova
    pablo_hue
    promodeler_rg
    rapid
    squiclean_b
