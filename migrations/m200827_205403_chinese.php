<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200827_205403_chinese extends Migration
{
    public function safeUp()
    {
        $this->insert('support_level', [
            'id' => 5,
            'name' => 'Machine',
        ]);

        $this->insert('language', [
            'lang' => 'zh-CN',
            'name' => '简体中文',
            'name_en' => 'Chinese (Simplified)',
            'support_level_id' => 5,
        ]);

        $this->batchInsert(
            'charset',
            ['name', 'php_name', 'substitute', 'is_unicode', 'order'],
            [
                ['GBK(GB 2312)', 'CP936', 63, false, 14],
                ['GB 18030', 'GB18030', 63, false, 15],
            ],
        );

        $chinese = $this->getChineseLanguageId();
        $this->batchInsert(
            'language_charset',
            ['language_id', 'charset_id', 'is_win_acp'],
            array_map(
                fn (array $_): array => [$chinese, $_[0], $_[1]],
                $this->getCharsetIds(),
            ),
        );

        $this->insert('accept_language', [
            'rule' => 'zh*',
            'language_id' => $chinese,
        ]);
    }

    public function safeDown()
    {
        $chinese = $this->getChineseLanguageId();
        $this->delete('accept_language', ['language_id' => $chinese]);
        $this->delete('language_charset', ['language_id' => $chinese]);
        $this->delete('language', ['id' => $chinese]);
        $this->delete('charset', ['id' => array_map(
            fn (array $_): int => $_[0],
            $this->getChineseCharsetIds(),
        )]);
        $this->delete('support_level', ['id' => 5]);
    }

    public function getChineseLanguageId(): int
    {
        return (int)((new Query())
            ->select('id')
            ->from('language')
            ->where(['lang' => 'zh-CN'])
            ->scalar()
        );
    }

    public function getCharsetIds(): array
    {
        return array_merge(
            $this->getChineseCharsetIds(),
            $this->getUnicodeCharsetIds(),
        );
    }

    public function getChineseCharsetIds(): array
    {
        return array_map(
            fn (array $row): array => [(int)$row['id'], $row['php_name'] === 'CP936'],
            (new Query())
                ->select('*')
                ->from('charset')
                ->where(['php_name' => ['CP936', 'GB18030']])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
        );
    }

    public function getUnicodeCharsetIds(): array
    {
        return array_map(
            fn ($value): array => [(int)$value, false],
            (new Query())
                ->select('id')
                ->from('charset')
                ->where(['php_name' => ['UTF-8', 'UTF-16LE']])
                ->orderBy(['id' => SORT_ASC])
                ->column(),
        );
    }
}
