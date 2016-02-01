<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Ramsey\Uuid\Uuid;
use Yii;

/**
 * This is the model class for table "blog_entry".
 *
 * @property integer $id
 * @property string $uuid
 * @property string $url
 * @property string $title
 * @property string $at
 */
class BlogEntry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog_entry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'url', 'title', 'at'], 'required'],
            [['at'], 'safe'],
            [['url', 'title'], 'string', 'max' => 256],
            [['uuid'], 'string'],
            [['uuid'], function ($attribute, $params) {
                if ($this->hasErrors($attribute)) {
                    return;
                }
                if (!Uuid::isValid($this->$attribute)) {
                    $this->addErrors($attribute, 'invalid uuid given');
                    return;
                }
                // normalize
                $this->$attribute = strtolower(
                    Uuid::fromString($this->$attribute)
                        ->toString()
                );
            }],
            [['uuid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'url' => 'Url',
            'title' => 'Title',
            'at' => 'At',
        ];
    }
}
