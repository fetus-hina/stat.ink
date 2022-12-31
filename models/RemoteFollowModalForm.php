<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class RemoteFollowModalForm extends Model
{
    public $screen_name;
    public $account;
    public $host_name;

    public static function factory()
    {
        return Yii::createObject(self::class);
    }

    public function rules()
    {
        return [
            [['screen_name', 'account'], 'required'],
            [['screen_name'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
            [['account'], 'string'],
            [['account'], 'match', 'pattern' => '/^[a-zA-Z0-9_]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/'],
            [['host_name'], 'filter', 'skipOnEmpty' => false,
                'filter' => function () {
                    if ($this->hasErrors('account')) {
                        $this->host_name = null;
                        return;
                    }
                    if (!preg_match('/@([a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*)$/', $this->account, $match)) {
                        $this->host_name = null;
                        return;
                    }
                    return strtolower($match[1]);
                },
            ],
            [['host_name'], 'required'],
            [['host_name'], 'validateHasIpAddr'],
        ];
    }

    public function validateHasIpAddr($attribute)
    {
        if ($this->hasErrors('host_name')) {
            return;
        }

        $value = $this->$attribute;
        foreach ([DNS_A, DNS_AAAA] as $type) {
            if (@dns_get_record($value, $type)) {
                return;
            }
        }

        $this->addError('account', 'サーバ名が正しくありません');
    }
}
