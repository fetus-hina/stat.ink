<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\api\v2;

use Override;

class PostHeadgearForm extends BaseGearForm
{
    #[Override]
    public function getType(): string
    {
        return 'headgear';
    }
}
