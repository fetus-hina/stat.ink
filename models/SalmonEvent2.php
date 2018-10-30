<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_event2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $splatnet
 */
class SalmonEvent2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_event2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 64],
            [['splatnet'], 'string', 'max' => 32],
            [['key'], 'unique'],
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
        ];
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-salmon-event2', $this->name),
        ];
    }
}
