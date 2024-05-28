<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;
use yii\db\Expression;

final class m240528_105806_season8 extends Migration
{
    private const KEY = 'season202406';
    private const NAME = 'Sizzle Season 2024';
    private const START_AT = '2024-06-01T00:00:00+00:00';
    private const END_AT = '2024-09-01T00:00:00+00:00';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->insert('{{%season3}}', [
            'key' => self::KEY,
            'name' => self::NAME,
            'start_at' => self::START_AT,
            'end_at' => self::END_AT,
            'term' => new Expression(
                vsprintf('tstzrange(%1$s::%4$s, %2$s::%4$s, %3$s)', [
                    $db->quoteValue(self::START_AT),
                    $db->quoteValue(self::END_AT),
                    $db->quoteValue('[]'),
                    'TIMESTAMP(0) WITH TIME ZONE',
                ]),
            ),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%season3}}', ['key' => self::KEY]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%season3}}',
        ];
    }
}
