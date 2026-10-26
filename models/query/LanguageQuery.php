<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\Language;
use yii\db\ActiveQuery;

/**
 * @extends ActiveQuery<Language>
 */
final class LanguageQuery extends ActiveQuery
{
    public function standard(): self
    {
        return $this->andWhere(['and',
            ['not', ['like', '{{language}}.[[lang]]', '%@%', false]],
        ]);
    }
}
