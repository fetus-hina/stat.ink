<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m220805_012235_korean extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%language}}', [
            'lang' => 'ko-KR',
            'name' => '한국어',
            'name_en' => 'Korean',
            'support_level_id' => 4,
        ]);

        $this->batchInsert(
            'charset',
            ['name', 'php_name', 'substitute', 'is_unicode', 'order'],
            [
                ['UHC', 'CP949', 63, false, 17],
                ['EUC-KR', 'EUC-KR', 63, false, 18],
            ],
        );

        $korean = $this->getKoreanLanguageId();
        $this->batchInsert(
            'language_charset',
            ['language_id', 'charset_id', 'is_win_acp'],
            array_map(
                fn (array $_): array => [$korean, $_[0], $_[1]],
                $this->getCharsetIds(),
            ),
        );

        $this->insert('accept_language', [
            'rule' => 'ko*',
            'language_id' => $korean,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $korean = $this->getKoreanLanguageId();
        $this->delete('accept_language', ['language_id' => $korean]);
        $this->delete('language_charset', ['language_id' => $korean]);
        $this->delete('language', ['id' => $korean]);
        $this->delete(
            'charset',
            [
                'id' => array_map(
                    fn (array $_): int => $_[0],
                    $this->getKoreanCharsetIds(),
                ),
            ],
        );
    }

    public function getKoreanLanguageId(): int
    {
        return (int)((new Query())
            ->select('id')
            ->from('language')
            ->where(['lang' => 'ko-KR'])
            ->scalar()
        );
    }

    public function getCharsetIds(): array
    {
        return array_merge(
            $this->getKoreanCharsetIds(),
            $this->getUnicodeCharsetIds(),
        );
    }

    public function getKoreanCharsetIds(): array
    {
        return array_map(
            fn (array $row): array => [(int)$row['id'], $row['php_name'] === 'CP949'],
            (new Query())
                ->select('*')
                ->from('charset')
                ->where(['php_name' => ['CP949', 'EUC-KR']])
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
