<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\AcceptLanguage;
use yii\db\ActiveQuery;

use function usort;

/**
 * @extends ActiveQuery<AcceptLanguage>
 */
final class AcceptLanguageQuery extends ActiveQuery
{
    public function all($db = null)
    {
        if ($list = parent::all($db)) {
            if ($list[0] instanceof AcceptLanguage) {
                usort($list, AcceptLanguage::compare(...));
            }
        }
        return $list;
    }
}
