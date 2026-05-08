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
 * This is the model class for table "passkey_aaguid".
 *
 * @property string $aaguid
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property PasskeyAaguidIcon[] $passkeyAaguidIcons
 */
class PasskeyAaguid extends ActiveRecord
{
    public static function tableName()
    {
        return 'passkey_aaguid';
    }

    #[Override]
    public function rules()
    {
        return [
            [['aaguid', 'name', 'created_at', 'updated_at'], 'required'],
            [['aaguid', 'name'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['aaguid'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'aaguid' => 'Aaguid',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getPasskeyAaguidIcons(): ActiveQuery
    {
        return $this->hasMany(PasskeyAaguidIcon::class, ['aaguid' => 'aaguid']);
    }
}
