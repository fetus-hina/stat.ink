<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\internal;

use yii\base\Model;

final class PatchBattle3UrlForm extends Model
{
    /**
     * @var string|null
     */
    public $link_url;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['link_url'], 'string'],
            [['link_url'], 'url',
                'validSchemes' => ['http', 'https'],
                'defaultScheme' => null,
                'enableIDN' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}
