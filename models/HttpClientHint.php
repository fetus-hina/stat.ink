<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use LogicException;
use Yii;
use app\models\ch\SfItem;
use app\models\ch\SfList;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\HeaderCollection;

use const SORT_STRING;

/**
 * This is the model class for table "http_client_hint".
 *
 * @property int $id
 * @property string $hash
 * @property array $value
 *
 * @property UserLoginHistory[] $userLoginHistories
 */
class HttpClientHint extends ActiveRecord
{
    public static function findOrCreate(?array $data = null): ?self
    {
        if ($data === null) {
            $data = static::createDataFromHeaders();
        }

        if (!$data) {
            return null;
        }
        ksort($data, SORT_STRING);

        $json = [];
        foreach ($data as $k => $v) {
            $json[$k] = (string)$v;
        }
        $jsonStr = Json::encode($json);
        $hash = rtrim(base64_encode(hash('sha3-256', $jsonStr, true)), '=');

        return Yii::$app->db->transactionEx(function () use ($jsonStr, $hash): ?self {
            if ($model = static::findOne(['hash' => $hash])) {
                return $model;
            }

            $model = Yii::createObject([
                'class' => static::class,
                'hash' => $hash,
                'value' => $jsonStr,
            ]);
            return $model->save() ? $model : null;
        });
    }

    public static function tableName()
    {
        return 'http_client_hint';
    }

    public function rules()
    {
        return [
            [['hash', 'value'], 'required'],
            [['value'], 'safe'],
            [['hash'], 'string', 'max' => 43],
            [['hash'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hash' => 'Hash',
            'value' => 'Value',
        ];
    }

    public function getUserLoginHistories(): ActiveQuery
    {
        return $this->hasMany(UserLoginHistory::class, ['client_hint_id' => 'id']);
    }

    public static function createDataFromHeaders(?HeaderCollection $headers = null): ?array
    {
        if ($headers === null) {
            $headers = Yii::$app->getRequest()->getHeaders();
        }

        $results = [];
        foreach (self::getSupportedHeaders() as $key => $type) {
            if ($headers->has($key)) {
                $value = self::convertHeaderValue((string)$headers->get($key), $type);
                if ($value !== null) {
                    $results[$key] = $value;
                }
            }
        }

        return $results ?: null;
    }

    private static function convertHeaderValue(string $value, string $type)
    {
        if ($type === 'list') {
            if (!$list = SfList::create($value)) {
                return null;
            }

            if (!$list->items) {
                return null;
            }

            foreach ($list->items as $item) {
                if (
                    !($item instanceof SfItem) ||
                    !is_string($item->value) ||
                    !preg_match('/\A[\x20-\x7e]+\z/', (string)$item->value)
                ) {
                    return null;
                }
            }

            return $list;
        }

        if (!$item = SfItem::create($value)) {
            return null;
        }

        switch ($type) {
            case 'string':
                if (
                    !is_string($item->value) ||
                    !preg_match('/\A[\x20-\x7e]+\z/', (string)$item->value)
                ) {
                    return null;
                }
                return $item;

            case 'boolean':
                return is_bool($item->value) ? $item : null;

            default:
                throw new LogicException('BUG');
        }
    }

    private static function getSupportedHeaders(): array
    {
        return [
            'sec-ch-ua' => 'list',
            'sec-ch-ua-arch' => 'string',
            'sec-ch-ua-full-version' => 'string',
            'sec-ch-ua-mobile' => 'boolean',
            'sec-ch-ua-model' => 'string',
            'sec-ch-ua-platform' => 'string',
            'sec-ch-ua-platform-version' => 'string',
        ];
    }
}
