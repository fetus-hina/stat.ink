<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200829_210631_traditional_chinese extends Migration
{
    public function safeUp()
    {
        $this->insert('language', [
            'lang' => 'zh-TW',
            'name' => '繁體中文',
            'name_en' => 'Chinese (Traditional)',
            'support_level_id' => 5,
        ]);

        $this->batchInsert(
            'charset',
            ['name', 'php_name', 'substitute', 'is_unicode', 'order'],
            [
                ['Big5', 'CP950', 63, false, 16],
            ],
        );

        $chinese = $this->getChineseLanguageId();
        $this->batchInsert(
            'language_charset',
            ['language_id', 'charset_id', 'is_win_acp'],
            array_map(
                function (array $_) use ($chinese): array {
                    return [$chinese, $_[0], $_[1]];
                },
                $this->getCharsetIds(),
            ),
        );

        $this->batchInsert('accept_language', ['rule', 'language_id'], [
            ['zh-hk', $chinese],
            ['zh-mo', $chinese],
            ['zh-tw', $chinese],
        ]);
    }

    public function safeDown()
    {
        $chinese = $this->getChineseLanguageId();
        $this->delete('accept_language', ['language_id' => $chinese]);
        $this->delete('language_charset', ['language_id' => $chinese]);
        $this->delete('language', ['id' => $chinese]);
        $this->delete('charset', ['id' => array_map(
            function (array $_): int {
                return $_[0];
            },
            $this->getChineseCharsetIds(),
        )]);
    }

    public function getChineseLanguageId(): int
    {
        return (int)((new Query())
            ->select('id')
            ->from('language')
            ->where(['lang' => 'zh-TW'])
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
            function (array $row): array {
                return [(int)$row['id'], $row['php_name'] === 'CP950'];
            },
            (new Query())
                ->select('*')
                ->from('charset')
                ->where(['php_name' => ['CP950']])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
        );
    }

    public function getUnicodeCharsetIds(): array
    {
        return array_map(
            function ($value): array {
                return [(int)$value, false];
            },
            (new Query())
                ->select('id')
                ->from('charset')
                ->where(['php_name' => ['UTF-8', 'UTF-16LE']])
                ->orderBy(['id' => SORT_ASC])
                ->column(),
        );
    }
}
