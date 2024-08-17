<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\models\SupportLevel;

final class m240817_064141_pt_br extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('language', [
            'lang' => 'pt-BR',
            'name' => 'Português do Brasil',
            'name_en' => 'Brazilian Portuguese',
            'support_level_id' => SupportLevel::PARTIAL,
        ]);

        $langId = $this->key2id('language', 'pt-BR', 'lang');
        $this->batchInsert('language_charset', ['language_id', 'charset_id', 'is_win_acp'], [
            [$langId, $this->key2id('charset', 'UTF-8', 'php_name'), false],
            [$langId, $this->key2id('charset', 'UTF-16LE', 'php_name'), false],
            [$langId, $this->key2id('charset', 'CP1252', 'php_name'), true],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('language', 'pt-BR', 'lang');

        $this->delete('language_charset', ['language_id' => $id]);
        $this->delete('language', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            'language',
            'language_charset',
        ];
    }
}
