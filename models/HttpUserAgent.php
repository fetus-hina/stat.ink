<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function mb_convert_encoding;
use function preg_replace;
use function trim;

/**
 * This is the model class for table "http_user_agent".
 *
 * @property integer $id
 * @property string $user_agent
 *
 * @property UserLoginHistory[] $userLoginHistories
 */
class HttpUserAgent extends ActiveRecord
{
    public static function findOrCreate(?string $ua = null): ?self
    {
        if ($ua === null) {
            $ua = (string)(Yii::$app->request->userAgent ?? '');
        }

        $ua = (string)@mb_convert_encoding($ua, 'UTF-8', 'UTF-8');
        $ua = preg_replace('/[^\x20-\x7e]+/', ' ', $ua);
        $ua = preg_replace('/\s+/', ' ', $ua);
        $ua = trim((string)$ua);
        if ($ua === '') {
            return null;
        }

        return Yii::$app->db->transactionEx(function () use ($ua): ?self {
            if ($model = static::findOne(['user_agent' => $ua])) {
                return $model;
            }

            $model = Yii::createObject([
                'class' => static::class,
                'user_agent' => $ua,
            ]);
            return $model->save() ? $model : null;
        });
    }

    public static function tableName()
    {
        return 'http_user_agent';
    }

    public function rules()
    {
        return [
            [['user_agent'], 'required'],
            [['user_agent'], 'string'],
            [['user_agent'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_agent' => 'User Agent',
        ];
    }

    public function getUserLoginHistories(): ActiveQuery
    {
        return $this->hasMany(UserLoginHistory::class, ['user_agent_id' => 'id']);
    }
}
