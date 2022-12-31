<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postSalmon;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\models\Salmon3;
use app\models\SalmonBoss3;
use app\models\SalmonBoss3Alias;
use app\models\SalmonBossAppearance3;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\base\Model;

final class BossForm extends Model
{
    use TypeHelperTrait;

    public $appearances;
    public $defeated;
    public $defeated_by_me;

    public function behaviors()
    {
        return [
            [
                'class' => TrimAttributesBehavior::class,
                'targets' => \array_keys($this->attributes),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['appearances', 'defeated', 'defeated_by_me'], 'required'],
            [['appearances', 'defeated', 'defeated_by_me'], 'integer', 'min' => 0],

            // Disabled these rules due to
            // https://github.com/frozenpandaman/s3s/issues/80#issuecomment-1328040023
            //
            // [['defeated', 'defeated_by_me'], 'compare',
            //     'compareAttribute' => 'appearances',
            //     'operator' => '<=',
            //     'type' => CompareValidator::TYPE_NUMBER,
            // ],
            // [['defeated_by_me'], 'compare',
            //     'compareAttribute' => 'defeated',
            //     'operator' => '<=',
            //     'type' => CompareValidator::TYPE_NUMBER,
            // ],
        ];
    }

    public function save(Salmon3 $salmon, string $bossKey): ?SalmonBossAppearance3
    {
        $bossId = self::key2id($bossKey, SalmonBoss3::class, SalmonBoss3Alias::class, 'salmonid_id');
        if (!$bossId) {
            return null;
        }

        $model = Yii::createObject([
            'class' => SalmonBossAppearance3::class,
            'salmon_id' => $salmon->id,
            'boss_id' => $bossId,
            'appearances' => $this->appearances,
            'defeated' => $this->defeated,
            'defeated_by_me' => $this->defeated_by_me,
        ]);

        // not save when no appearances but it's OK
        if ($this->appearances < 1) {
            return $model;
        }

        return $model->save() ? $model : null;
    }
}
