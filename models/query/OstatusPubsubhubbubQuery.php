<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use app\components\helpers\db\Now;
use app\models\OstatusPubsubhubbub;
use yii\db\ActiveQuery;

/**
 * @extends ActiveQuery<OstatusPubsubhubbub>
 */
final class OstatusPubsubhubbubQuery extends ActiveQuery
{
    public function active(): self
    {
        return $this->andWhere(['or',
            ['lease_until' => null],
            ['>=', 'lease_until', new Now()],
        ]);
    }
}
