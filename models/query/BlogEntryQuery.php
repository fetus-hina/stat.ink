<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\BlogEntry;
use yii\db\ActiveQuery;

use const SORT_DESC;

/**
 * @extends ActiveQuery<BlogEntry>
 */
final class BlogEntryQuery extends ActiveQuery
{
    public function latest(): self
    {
        return $this->orderBy(['{{blog_entry}}.[[at]]' => SORT_DESC]);
    }
}
