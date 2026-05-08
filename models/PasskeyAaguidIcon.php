<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "passkey_aaguid_icon".
 *
 * @property string $aaguid
 * @property string $theme
 * @property string $mime_type
 * @property resource $data
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PasskeyAaguid $aagu
 */
class PasskeyAaguidIcon extends ActiveRecord
{
    public static function tableName()
    {
        return 'passkey_aaguid_icon';
    }

    #[Override]
    public function rules()
    {
        return [
            [['aaguid', 'theme', 'mime_type', 'data', 'created_at', 'updated_at'], 'required'],
            [['aaguid', 'data'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['theme'], 'string', 'max' => 8],
            [['mime_type'], 'string', 'max' => 32],
            [['aaguid', 'theme'], 'unique', 'targetAttribute' => ['aaguid', 'theme']],
            [['aaguid'], 'exist', 'skipOnError' => true, 'targetClass' => PasskeyAaguid::class, 'targetAttribute' => ['aaguid' => 'aaguid']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'aaguid' => 'Aaguid',
            'theme' => 'Theme',
            'mime_type' => 'Mime Type',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAagu(): ActiveQuery
    {
        return $this->hasOne(PasskeyAaguid::class, ['aaguid' => 'aaguid']);
    }
}
