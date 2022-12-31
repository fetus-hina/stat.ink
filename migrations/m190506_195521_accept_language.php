<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m190506_195521_accept_language extends Migration
{
    public function safeUp()
    {
        $this->createTable('accept_language', [
            'id'            => $this->primaryKey(),
            // (1*8ALPHA *("-" 1*8alphanum)) / "*"
            'rule'          => $this->string(8 + 1 + 8)->notNull()->unique(),
            'language_id'   => $this->pkRef('language')->notNull(),
            sprintf('CHECK ((%s))', implode(') OR (', [
                "[[rule]] = '*'",
                "[[rule]] ~ '^[a-z]{1,8}\\*?$'", // "ja" or "ja*"
                "[[rule]] ~ '^[a-z]{1,8}-(?:\\*|[a-z0-9]{1,8})$'", // "ja-*" or "ja-jp"
            ])),
        ]);
        $lang = $this->getLanguages();
        $this->batchInsert('accept_language', ['rule', 'language_id'], [
            ['en*',     $lang['en-US']],
            ['en-au',   $lang['en-GB']],
            ['en-gb',   $lang['en-GB']],
            ['es*',     $lang['es-MX']],
            ['es-es',   $lang['es-ES']],
            ['fr*',     $lang['fr-FR']],
            ['fr-ca',   $lang['fr-CA']],
            ['it*',     $lang['it-IT']],
            ['ja*',     $lang['ja-JP']],
            ['nl*',     $lang['nl-NL']],
            ['ru*',     $lang['ru-RU']],
            ['*',       $lang['en-US']],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('accept_language');
    }

    public function getLanguages(): array
    {
        return ArrayHelper::map(
            (new Query())->select('*')->from('language')->all(),
            'lang',
            'id',
        );
    }
}
