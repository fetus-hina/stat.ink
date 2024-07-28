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

final class m240726_133954_calc_played_with3_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $pgsqlVersion = $db->createCommand('SHOW SERVER_VERSION')->queryScalar();

        $sql = vsprintf('CREATE FUNCTION %s (%s) RETURNS CHAR(32) %s AS %s', [
            $db->quoteColumnName('calc_played_with3_id'),
            implode(', ', [
                'IN ' . $db->quoteColumnName('name') . ' TEXT',
                'IN ' . $db->quoteColumnName('number') . ' TEXT',
            ]),
            implode(
                ' ',
                array_filter(
                    [
                        'LANGUAGE SQL',
                        'IMMUTABLE',
                        'RETURNS NULL ON NULL INPUT',
                        'SECURITY INVOKER',
                        version_compare($pgsqlVersion, '9.6.0', '>=') ? 'PARALLEL SAFE' : null,
                    ],
                    fn (?string $v): bool => $v !== null,
                ),
            ),
            $db->quoteValue(
                "SELECT LEFT(ENCODE(SHA256((\$1 || ' #' || \$2)::bytea), 'hex'), 32)",
            ),
        ]);
        $this->execute($sql);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('DROP FUNCTION [[calc_played_with3_id]] (TEXT, TEXT)');

        return true;
    }
}
