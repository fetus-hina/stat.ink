<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m261125_080413_accept_language_zh_script extends Migration
{
    private const CHECK_NAME = 'accept_language_rule_check';

    private const CHECK_PATTERNS_BASE = [
        "[[rule]] = '*'",
        "[[rule]] ~ '^[a-z]{1,8}\\*?$'", // "ja" or "ja*"
        "[[rule]] ~ '^[a-z]{1,8}-(?:\\*|[a-z0-9]{1,8})$'", // "ja-*" or "ja-jp"
    ];

    // "zh-hant-*", "zh-hant-tw" or "es-latn-419" (language-script-region, max 8+1+4+1+3=17 chars)
    private const CHECK_PATTERN_SCRIPT = "[[rule]] ~ '^[a-z]{1,8}-[a-z]{4}-(?:\\*|[a-z]{2}|[0-9]{3})$'";

    private const RULES = [
        'zh-hans' => 'zh-CN',
        'zh-hans-*' => 'zh-CN',
        'zh-hant' => 'zh-TW',
        'zh-hant-*' => 'zh-TW',
    ];

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->replaceRuleCheck([...self::CHECK_PATTERNS_BASE, self::CHECK_PATTERN_SCRIPT]);

        $languageIds = $this->getLanguageIds();

        $rows = [];
        foreach (self::RULES as $rule => $lang) {
            $rows[] = [$rule, $languageIds[$lang]];
        }

        $this->batchInsert('{{%accept_language}}', ['rule', 'language_id'], $rows);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->delete('{{%accept_language}}', ['rule' => array_keys(self::RULES)]);
        $this->replaceRuleCheck(self::CHECK_PATTERNS_BASE);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%accept_language}}',
        ];
    }

    /**
     * @param list<string> $patterns
     */
    private function replaceRuleCheck(array $patterns): void
    {
        $this->dropCheck(self::CHECK_NAME, '{{%accept_language}}');
        $this->addCheck(
            self::CHECK_NAME,
            '{{%accept_language}}',
            '(' . implode(') OR (', $patterns) . ')',
        );
    }

    /**
     * @return array<string, int>
     */
    private function getLanguageIds(): array
    {
        $ids = [];
        foreach (array_unique(array_values(self::RULES)) as $lang) {
            $id = (new Query())
                ->select('id')
                ->from('{{%language}}')
                ->where(['lang' => $lang])
                ->scalar();
            if (!is_int($id) && !is_string($id)) {
                throw new RuntimeException("Language not found: {$lang}");
            }

            $ids[$lang] = (int)$id;
        }

        return $ids;
    }
}
