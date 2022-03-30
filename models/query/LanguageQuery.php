<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use yii\db\ActiveQuery;

final class LanguageQuery extends ActiveQuery
{
    public function standard(): self
    {
        return $this->andWhere(['and',
            ['not', ['like', '{{language}}.[[lang]]', '%@%', false]],
        ]);
    }
}
