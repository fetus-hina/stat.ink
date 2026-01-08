<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class EmailForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            [['email'], 'trim'],
            [['email'], 'email',
                'checkDNS' => true,
                'enableIDN' => false,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
        ];
    }
}
