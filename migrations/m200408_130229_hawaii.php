<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;

class m200408_130229_hawaii extends Migration
{
    public function safeUp()
    {
        $polynesia = (new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['timezone_group.name' => 'Polynesia'])
            ->scalar();

        $pitcairnOrder = (new Query())
            ->select('[[order]]')
            ->from('timezone')
            ->where(['timezone.identifier' => 'Pacific/Pitcairn'])
            ->scalar();

        // 目的の場所以上の order を持つタイムゾーンを一つずつずらす
        $this->update(
            'timezone',
            [
                'order' => new Expression(vsprintf('%s.%s + 10000001', [
                    $this->db->quoteTableName('timezone'),
                    $this->db->quoteColumnName('order'),
                ])),
            ],
            ['and',
                ['>=', 'timezone.order', $pitcairnOrder],
            ],
        );
        $this->update(
            'timezone',
            [
                'order' => new Expression(vsprintf('%s.%s - 10000000', [
                    $this->db->quoteTableName('timezone'),
                    $this->db->quoteColumnName('order'),
                ])),
            ],
            ['and',
                ['>=', 'timezone.order', $pitcairnOrder + 10000001],
            ],
        );

        // ハワイを目的の位置に入れる
        $this->update(
            'timezone',
            [
                'group_id' => $polynesia,
                'order' => $pitcairnOrder,
            ],
            ['identifier' => 'Pacific/Honolulu'],
        );
    }

    public function safeDown()
    {
        // ハワイを元の位置に戻す
        $oceania = (new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['timezone_group.name' => 'Australia/Oceania'])
            ->scalar();

        $hawaiiOldOrder = (new Query())
            ->select('order')
            ->from('timezone')
            ->where(['identifier' => 'Pacific/Honolulu'])
            ->scalar();

        $guamOrder = (new Query())
            ->select('order')
            ->from('timezone')
            ->where(['identifier' => 'Pacific/Guam'])
            ->scalar();

        $this->update(
            'timezone',
            [
                'order' => $guamOrder - 1,
                'group_id' => $oceania,
            ],
            ['identifier' => 'Pacific/Honolulu'],
        );

        // hawaiiOldOrder より大きい order のタイムゾーンを戻す
        $this->update(
            'timezone',
            [
                'order' => new Expression(vsprintf('%s.%s + 10000000', [
                    $this->db->quoteTableName('timezone'),
                    $this->db->quoteColumnName('order'),
                ])),
            ],
            ['and',
                ['>=', 'timezone.order', $hawaiiOldOrder],
            ],
        );
        $this->update(
            'timezone',
            [
                'order' => new Expression(vsprintf('%s.%s - 10000001', [
                    $this->db->quoteTableName('timezone'),
                    $this->db->quoteColumnName('order'),
                ])),
            ],
            ['and',
                ['>=', 'timezone.order', $hawaiiOldOrder + 10000000],
            ],
        );
    }
}
