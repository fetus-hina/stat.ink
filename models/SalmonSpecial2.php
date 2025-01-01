<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_special2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $splatnet
 * @property integer $special_id
 *
 * @property Special2 $special
 */
class SalmonSpecial2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_special2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'special_id'], 'required'],
            [['splatnet', 'special_id'], 'default', 'value' => null],
            [['splatnet', 'special_id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['special_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Special2::class,
                'targetAttribute' => ['special_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'splatnet' => 'Splatnet',
            'special_id' => 'Special ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSpecial()
    {
        return $this->hasOne(Special2::class, ['id' => 'special_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-special2', $this->name),
        ];
    }
}
