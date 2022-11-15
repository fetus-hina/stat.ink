<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author eli <frozenpandaman@users.noreply.github.com>
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
use yii\validators\CompareValidator;

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

            [['defeated', 'defeated_by_me'], 'compare',
                'compareAttribute' => 'appearances',
                'operator' => '<=',
                'type' => CompareValidator::TYPE_NUMBER,
            ],
            [['defeated_by_me'], 'compare',
                'compareAttribute' => 'defeated',
                'operator' => '<=',
                'type' => CompareValidator::TYPE_NUMBER,
            ],
        ];
    }

    public function save(Salmon3 $salmon, string $bossKey): ?SalmonBossAppearance3
    {
        if (!$bossId = $this->getBossId($bossKey)) {
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

    private function getBossId(string $bossKey): ?int
    {
        $boss = SalmonBoss3::find()
            ->andWhere(['key' => $bossKey])
            ->limit(1)
            ->one();
        if ($boss) {
            return $boss->id;
        }

        $boss = SalmonBoss3Alias::find()
            ->andWhere(['key' => $bossKey])
            ->limit(1)
            ->one();
        if ($boss) {
            return $boss->salmonid->id;
        }

        return null;
    }
}
