<?php

namespace tests\models;

use app\components\helpers\Blackout;
use app\models\User;

class BlackoutListTest extends \Codeception\Test\Unit
{
    public function testNoBlackout()
    {
        $modes = ['standard', 'squad_2', 'squad_3', 'squad_4', 'private'];
        foreach ($modes as $mode) {
            foreach (range(1, 8) as $i) {
                $list = Blackout::getBlackoutTargetList($mode, User::BLACKOUT_NOT_BLACKOUT, $i);
                $this->assertTrue(is_array($list));
                $this->assertEquals(0, count($list));
            }
        }
    }

    public function testAlwaysBlackout()
    {
        $modes = ['standard', 'squad_2', 'squad_3', 'squad_4', 'private'];
        foreach ($modes as $mode) {
            foreach (range(1, 8) as $i) {
                $list = Blackout::getBlackoutTargetList($mode, User::BLACKOUT_ALWAYS, $i);
                $this->assertTrue(is_array($list));
                $this->assertEquals(7, count($list));
                foreach (range(1, 8) as $j) {
                    if ($i === $j) {
                        $this->assertNotContains($j, $list);
                    } else {
                        $this->assertContains($j, $list);
                    }
                }
            }
        }
    }

    public function testBlackoutOnNotPrivateMode()
    {
        // "not-private" のとき、プラベ以外は7人つぶし
        $modes = ['standard', 'squad_2', 'squad_3', 'squad_4'];
        foreach ($modes as $mode) {
            foreach (range(1, 8) as $i) {
                $list = Blackout::getBlackoutTargetList($mode, User::BLACKOUT_NOT_PRIVATE, $i);
                $this->assertTrue(is_array($list));
                $this->assertEquals(7, count($list));
                foreach (range(1, 8) as $j) {
                    if ($i === $j) {
                        $this->assertNotContains($j, $list);
                    } else {
                        $this->assertContains($j, $list);
                    }
                }
            }
        }

        // プラベの時は誰もつぶさない
        foreach (range(1, 8) as $i) {
            $list = Blackout::getBlackoutTargetList('private', User::BLACKOUT_NOT_PRIVATE, $i);
            $this->assertTrue(is_array($list));
            $this->assertEmpty($list);
        }
    }

    public function testBlackoutEnemyTeamInSquadBattle()
    {
        $modes = ['squad_3', 'squad_4'];
        foreach ($modes as $mode) {
            foreach (range(1, 4) as $i) {
                $list = Blackout::getBlackoutTargetList($mode, User::BLACKOUT_NOT_FRIEND, $i);
                $this->assertTrue(is_array($list));
                $this->assertEquals(4, count($list));

                // 自チーム
                foreach (range(1, 4) as $j) {
                    $this->assertNotContains($j, $list);
                }

                // 敵チーム
                foreach (range(5, 8) as $j) {
                    $this->assertContains($j, $list);
                }
            }

            foreach (range(5, 8) as $i) {
                $list = Blackout::getBlackoutTargetList($mode, User::BLACKOUT_NOT_FRIEND, $i);
                $this->assertTrue(is_array($list));
                $this->assertEquals(4, count($list));

                // 自チーム
                foreach (range(5, 8) as $j) {
                    $this->assertNotContains($j, $list);
                }

                // 敵チーム
                foreach (range(1, 4) as $j) {
                    $this->assertContains($j, $list);
                }
            }
        }
    }

    public function testBlackoutEnemyTeamInPrivateBattle()
    {
        // プラベは全員消さない
        foreach (range(1, 8) as $i) {
            $list = Blackout::getBlackoutTargetList('private', User::BLACKOUT_NOT_FRIEND, $i);
            $this->assertTrue(is_array($list));
            $this->assertEmpty($list);
        }
    }

    public function testBlackoutEnemyTeamInOtherMode()
    {
        $modes = ['standard', 'squad_2'];
        foreach ($modes as $mode) {
            foreach (range(1, 8) as $i) {
                $list = Blackout::getBlackoutTargetList($mode, User::BLACKOUT_NOT_FRIEND, $i);
                $this->assertTrue(is_array($list));
                $this->assertEquals(7, count($list));
                foreach (range(1, 8) as $j) {
                    if ($i === $j) {
                        $this->assertNotContains($j, $list);
                    } else {
                        $this->assertContains($j, $list);
                    }
                }
            }
        }
    }
}
