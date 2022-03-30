<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Country;
use app\models\Timezone;
use yii\db\Migration;

class m160108_142541_tz_country extends Migration
{
    public function up()
    {
        $this->createTable('timezone_country', [
            'timezone_id' => 'INTEGER NOT NULL',
            'country_id' => 'INTEGER NOT NULL',
        ]);
        $this->addPrimaryKey('pk_timezone_country', 'timezone_country', ['timezone_id', 'country_id']);
        $this->addForeignKey('fk_timezone_country_1', 'timezone_country', 'timezone_id', 'timezone', 'id');
        $this->addForeignKey('fk_timezone_country_2', 'timezone_country', 'country_id', 'country', 'id');

        $tz = $this->getTimezones();
        $cc = $this->getCountries();
        $this->batchInsert('timezone_country', ['timezone_id', 'country_id'], [
            [ $tz['Asia/Tokyo'],            $cc['jp'] ],

            [ $tz['Europe/Athens'],         $cc['eu'] ],
            [ $tz['Europe/Paris'],          $cc['eu'] ],
            [ $tz['Europe/London'],         $cc['eu'] ],

            [ $tz['America/New_York'],      $cc['us'] ],
            [ $tz['America/New_York'],      $cc['ca'] ],
            [ $tz['America/Chicago'],       $cc['us'] ],
            [ $tz['America/Chicago'],       $cc['ca'] ],
            [ $tz['America/Denver'],        $cc['us'] ],
            [ $tz['America/Denver'],        $cc['ca'] ],
            [ $tz['America/Los_Angeles'],   $cc['us'] ],
            [ $tz['America/Los_Angeles'],   $cc['ca'] ],

            [ $tz['America/Phoenix'],       $cc['us'] ],
            [ $tz['America/Anchorage'],     $cc['us'] ],
            [ $tz['America/Adak'],          $cc['us'] ],
            [ $tz['Pacific/Honolulu'],      $cc['us'] ],
            [ $tz['America/St_Johns'],      $cc['ca'] ],
            [ $tz['America/Halifax'],       $cc['ca'] ],
            [ $tz['America/Regina'],        $cc['ca'] ],

            [ $tz['Australia/Brisbane'],    $cc['au'] ],
            [ $tz['Australia/Sydney'],      $cc['au'] ],
            [ $tz['Australia/Adelaide'],    $cc['au'] ],
            [ $tz['Australia/Darwin'],      $cc['au'] ],
            [ $tz['Australia/Perth'],       $cc['au'] ],
        ]);
    }

    public function down()
    {
        $this->dropTable('timezone_country');
    }

    public function getTimezones()
    {
        $ret = [];
        foreach (Timezone::find()->all() as $tz) {
            $ret[$tz->identifier] = $tz->id;
        }
        return $ret;
    }

    public function getCountries()
    {
        $ret = [];
        foreach (Country::find()->all() as $cc) {
            $ret[$cc->key] = $cc->id;
        }
        return $ret;
    }
}
