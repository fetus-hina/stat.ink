<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use app\components\helpers\db\Now;
use yii\db\ActiveQuery;

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
