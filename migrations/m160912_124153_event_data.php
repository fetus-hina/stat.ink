<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160912_124153_event_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('event', ['date', 'name', 'icon'], [
            ['2015-05-28T00:00:00+09', 'On sale', 'squid'],
            ['2015-06-02T11:00:00+09', 'N-ZAP \'85', 'release_weapon'],
            ['2015-06-06T11:00:00+09', 'Inkbrush', 'release_weapon'],
            ['2015-06-13T11:00:00+09', 'Splash-o-matic', 'release_weapon'],
            ['2015-06-13T18:00:00+09', 'Splatfest JP #1', 'splatfest'],
            ['2015-06-17T11:00:00+09', 'L-3 Nozzlenose, Custom E-liter 3K', 'release_weapon'],
            ['2015-06-24T11:00:00+09', 'Luna Blaster', 'release_weapon'],
            ['2015-06-27T11:00:00+09', 'Custom Dual Squelcher, Carbon Roller', 'release_weapon'],
            ['2015-07-03T15:00:00+09', 'Splatfest JP #2', 'splatfest'],
            ['2015-07-08T11:00:00+09', '.96 Gal Deco, Sploosh-o-matic', 'release_weapon'],
            ['2015-07-18T11:00:00+09', 'N-ZAP \'89, Octobrush', 'release_weapon'],
            ['2015-07-22T11:00:00+09', 'Neo Splash-o-matic, E-liter 3K Scope', 'release_weapon'],
            ['2015-07-25T15:00:00+09', 'Splatfest JP #3', 'splatfest'],
            ['2015-08-01T11:00:00+09', 'Range Blaster, Inkbrush Nouveau', 'release_weapon'],
            ['2015-08-06T11:00:00+09', 'Slosher, Heavy Splatling', 'release_weapon'],
            ['2015-08-22T12:00:00+09', 'Splatfest JP #4', 'splatfest'],
            ['2015-08-29T11:00:00+09', 'L-3 Nozzlenose D, Bamboozler 14 Mk I', 'release_weapon'],
            ['2015-09-05T11:00:00+09', 'Mini Splatling', 'release_weapon'],
            ['2015-09-12T11:00:00+09', 'H-3 Nozzlenose', 'release_weapon'],
            ['2015-09-12T12:00:00+09', 'Splatfest JP #5', 'splatfest'],
            ['2015-09-26T11:00:00+09', 'Tri-Slosher', 'release_weapon'],
            ['2015-10-03T11:00:00+09', 'Carbon Roller Deco', 'release_weapon'],
            ['2015-10-10T09:00:00+09', 'Splatfest JP #6', 'splatfest'],
            ['2015-10-10T11:00:00+09', 'Custom Range Blaster, Custom E-liter 3K Scope', 'release_weapon'],
            ['2015-10-17T11:00:00+09', 'Rapid Blaster Pro', 'release_weapon'],
            ['2015-10-30T11:00:00+09', 'Luna Blaster Neo, H-3 Nozzlenose D', 'release_weapon'],
            ['2015-10-31T09:00:00+09', 'Splatfest JP #7', 'splatfest'],
            ['2015-11-07T11:00:00+09', 'Heavy Splatling Deco', 'release_weapon'],
            ['2015-11-11T11:00:00+09', 'Neo Sploosh-o-matic', 'release_weapon'],
            ['2015-11-18T11:00:00+09', 'Bamboozler 14 Mk II', 'release_weapon'],
            ['2015-11-21T11:00:00+09', 'Hydra Splatling', 'release_weapon'],
            ['2015-11-21T12:00:00+09', 'Splatfest JP #8', 'splatfest'],
            ['2015-11-25T11:00:00+09', 'Slosher Deco', 'release_weapon'],
            ['2015-11-28T11:00:00+09', 'Sloshing Machine', 'release_weapon'],
            ['2015-12-12T11:00:00+09', 'Zink Mini Splatling', 'release_weapon'],
            ['2015-12-19T11:00:00+09', 'Tri-Slosher Nouveau', 'release_weapon'],
            ['2015-12-25T11:00:00+09', 'Rapid Blaster Pro Deco', 'release_weapon'],
            ['2015-12-26T09:00:00+09', 'Splatfest JP #9', 'splatfest'],
            ['2016-01-01T19:00:00+09', 'Octobrush Nouveau', 'release_weapon'],
            ['2016-01-09T11:00:00+09', 'Sloshing Machine Neo', 'release_weapon'],
            ['2016-01-16T11:00:00+09', 'Custom Hydra Splatling', 'release_weapon'],
            ['2016-01-23T12:00:00+09', 'Splatfest JP #10', 'splatfest'],
            ['2016-02-20T06:00:00+09', 'Splatfest #11', 'splatfest'],
            ['2016-03-12T09:00:00+09', 'Splatfest JP #12', 'splatfest'],
            ['2016-04-13T11:00:00+09', 'Sheldon\'s Picks Vol.1', 'sheldon'],
            ['2016-04-23T09:00:00+09', 'Splatfest JP #13', 'splatfest'],
            ['2016-05-14T12:00:00+09', 'Splatfest #14', 'splatfest'],
            ['2016-06-08T11:00:00+09', 'Sheldon\'s Picks Vol.2', 'sheldon'],
            ['2016-06-18T09:00:00+09', 'Splatfest JP #15', 'splatfest'],
            ['2016-07-22T12:00:00+09', 'Final Splatfest', 'splatfest'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('event');
    }
}
