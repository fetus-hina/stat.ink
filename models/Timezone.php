<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "timezone".
 *
 * @property integer $id
 * @property string $identifier
 * @property string $name
 * @property integer $order
 */
class Timezone extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return parent::find()->orderBy('{{timezone}}.[[order]] ASC');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timezone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identifier', 'name'], 'required'],
            [['order'], 'integer'],
            [['identifier', 'name'], 'string', 'max' => 32],
            [['identifier'], 'unique'],
            [['name'], 'unique'],
            [['"order"'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identifier' => 'Identifier',
            'name' => 'Name',
            'order' => 'Order',
        ];
    }
}
