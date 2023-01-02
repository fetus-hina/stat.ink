<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use app\components\behaviors\FixAttributesBehavior;

use function array_merge;

class PostShoesForm extends BaseGearForm
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => FixAttributesBehavior::class,
                'attributes' => [
                    'gear' => [
                        'sea_slug_volt_950s' => 'sea_slug_volt_95s',
                    ],
                ],
            ],
        ]);
    }

    public function getType()
    {
        return 'shoes';
    }
}
