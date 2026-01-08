<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v2\salmon;

use app\models\Salmon2;
use app\models\SalmonMap2;
use app\models\User;
use yii\base\Model;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;

use const SORT_ASC;
use const SORT_DESC;

class IndexFilterForm extends Model
{
    public $screen_name;
    public $only;
    public $stage;
    public $newer_than;
    public $older_than;
    public $order;
    public $count;

    public function rules()
    {
        // {{{
        return [
            [['screen_name'], 'required',
                'when' => fn (self $model): bool => ($model->only === 'splatnet_number') ||
                        ($model->order === 'splatnet_asc') ||
                        ($model->order === 'splatnet_desc'),
            ],
            [['screen_name'], 'string'],
            [['screen_name'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['screen_name' => 'screen_name'],
            ],
            [['only'], 'string'],
            [['only'], 'in',
                'range' => [
                    'splatnet_number',
                ],
            ],
            [['stage'], 'string'],
            [['stage'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['stage' => 'key'],
            ],
            [['newer_than', 'older_than'], 'integer', 'min' => 1],
            [['order'], 'string'],
            [['order'], 'in',
                'range' => [
                    'asc',
                    'desc',
                    'splatnet_asc',
                    'splatnet_desc',
                ],
            ],
            [['count'], 'integer',
                'min' => 1,
                'max' => 50,
                'when' => fn (self $model): bool => $model->only !== 'splatnet_number',
            ],
            [['count'], 'integer',
                'min' => 1,
                'max' => 1000,
                'when' => fn (self $model): bool => $model->only === 'splatnet_number',
            ],
        ];
        // }}}
    }

    public function find(): ?ActiveQuery
    {
        if (!$this->validate()) {
            return null;
        }

        $query = Salmon2::find()
            ->orderBy(['id' => SORT_DESC])
            ->with([
                'agent',
                'bossAppearances',
                'failReason',
                'players',
                'stage',
                'titleAfter',
                'titleBefore',
                'user',
                'waves',
            ]);

        return $this->decorateQuery($query);
    }

    protected function decorateQuery(ActiveQuery $query): ActiveQuery
    {
        $this->decorateQueryByUser($query);
        $this->decorateQueryByOnly($query);
        $this->decorateQueryByStage($query);
        $this->decorateQueryByIdRange($query);
        $this->decorateQueryByOrder($query);
        $this->decorateQueryByCount($query);

        return $query;
    }

    // decorateQueryByBlabla {{{
    protected function decorateQueryByUser(ActiveQuery $query): void
    {
        if ($this->screen_name == '') {
            return;
        }

        $user = User::findOne(['screen_name' => $this->screen_name]);
        if ($user) {
            $query->andWhere(['user_id' => $user->id]);
        } else {
            $query->andWhere('0 = 1');
        }
    }

    protected function decorateQueryByOnly(ActiveQuery $query): void
    {
        if ($this->only == '') {
            return;
        }

        switch ($this->only) {
            case 'splatnet_number':
                $query->andWhere(['not', ['splatnet_number' => null]]);
                $query->orderBy(['splatnet_number' => SORT_DESC]);
                break;

            default:
                throw new NotSupportedException("BUG: unknown \"only\" value: {$this->only}");
        }
    }

    protected function decorateQueryByStage(ActiveQuery $query): void
    {
        if ($this->stage == '') {
            return;
        }

        if ($stage = SalmonMap2::findOne(['key' => $this->stage])) {
            $query->andWhere(['stage_id' => $stage->id]);
        } else {
            $query->andWhere('0 = 1');
        }
    }

    protected function decorateQueryByIdRange(ActiveQuery $query): void
    {
        if ($this->newer_than > 0) {
            $query->andWhere(['>', 'id', $this->newer_than]);
        }

        if ($this->older_than > 0) {
            $query->andWhere(['<', 'id', $this->older_than]);
        }
    }

    protected function decorateQueryByOrder(ActiveQuery $query): void
    {
        switch ($this->order) {
            case 'asc':
            case 'desc':
                $query->orderBy([
                    'id' => $this->order == 'desc' ? SORT_DESC : SORT_ASC,
                ]);
                break;

            case 'splatnet_asc':
            case 'splatnet_desc':
                $query->orderBy([
                    'splatnet_number' => $this->order == 'splatnet_desc' ? SORT_DESC : SORT_ASC,
                ]);
                break;

            default:
                break;
        }
    }

    protected function decorateQueryByCount(ActiveQuery $query): void
    {
        $count = (int)$this->count > 0 ? (int)$this->count : 50;
        $query->limit($count);
    }

    // }}}
}
