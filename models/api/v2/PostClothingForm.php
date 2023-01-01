<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use app\components\behaviors\FixAttributesBehavior;

use function array_merge;

class PostClothingForm extends BaseGearForm
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => FixAttributesBehavior::class,
                'attributes' => [
                    'gear' => [
                        // https://github.com/frozenpandaman/splatnet2statink/commit/d6c6826e685ce2ccdedf4f941ba5b069d38df8d4
                        'kung-fu_zip_up' => 'kung_fu_zip_up',
                    ],
                ],
            ],
        ]);
    }

    public function getType()
    {
        return 'clothing';
    }
}
