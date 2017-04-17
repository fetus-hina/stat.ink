<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\behaviors\TimestampBehavior;
use phpseclib\Crypt\RSA;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ostatus_rsa".
 *
 * @property integer $user_id
 * @property integer $bits
 * @property string $privkey
 * @property string $pubkey
 * @property string $modulus
 * @property string $exponent
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class OstatusRsa extends ActiveRecord
{
    // should call save() after return
    public static function factory(int $user_id, int $bits = 1024) : self
    {
        $b64 = function (string $binary) : string {
            return strtr(base64_encode($binary), '+/', '-_');
        };

        $pair = (new RSA())->createKey($bits);
        $pub = new RSA();
        $pub->loadkey($pair['publickey']);
        return Yii::createObject([
            'class'     => static::class,
            'user_id'   => $user_id,
            'bits'      => $bits,
            'privkey'   => $pair['privatekey'],
            'pubkey'    => $pair['publickey'],
            'modulus'   => $b64($pub->modulus->toBytes()),
            'exponent'  => $b64($pub->exponent->toBytes()),
        ]);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ostatus_rsa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'bits', 'privkey', 'pubkey', 'modulus', 'exponent'], 'required'],
            [['user_id', 'bits'], 'integer'],
            [['privkey', 'pubkey', 'modulus', 'exponent'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'bits' => 'Bits',
            'privkey' => 'Private Key',
            'pubkey' => 'Public Key',
            'modulus' => 'Modulus',
            'exponent' => 'Exponent',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
