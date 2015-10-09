<?php
namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use app\models\DeathReasonType;

class DeathReasonGetForm extends Model
{
    public $type;

    public function rules()
    {
        return [
            [['type'], 'exist',
                'targetClass' => DeathReasonType::className(),
                'targetAttribute' => 'key'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function filterQuery(ActiveQuery $query)
    {
        if ($this->type) {
            $query->innerJoinWith('type');
            $query->andWhere(['{{death_reason_type}}.[[key]]' => $this->type]);
        }
        return $query;
    }
}
