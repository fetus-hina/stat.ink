<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\behaviors\AutoTrimAttributesBehavior;
use app\components\validators\IdnToPunycodeFilterValidator;
use yii\base\Model;

use function array_keys;
use function array_merge;
use function is_bool;
use function preg_match;
use function preg_replace;
use function sprintf;
use function trim;

class Battle2Form extends Model
{
    public $lobby_id;
    public $mode_id;
    public $rule_id;
    public $map_id;
    public $weapon_id;
    public $result;
    public $kill;
    public $death;
    public $kill_or_assist;
    public $special;
    public $rank_id;
    public $rank_after_id;
    public $my_point;
    public $link_url;
    public $note;
    public $private_note;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            AutoTrimAttributesBehavior::class,
        ]);
    }

    public function rules()
    {
        return [
            [['lobby_id', 'mode_id', 'rule_id', 'map_id', 'weapon_id'], 'integer'],
            [['kill', 'death', 'kill_or_assist', 'special'], 'integer', 'min' => 0, 'max' => 99],
            [['my_point'], 'integer', 'min' => 0],
            [['rank_id', 'rank_after_id'], 'integer'],
            [['result'], 'in', 'range' => ['win', 'lose']],
            [['link_url'], 'url', 'enableIDN' => true],
            [['link_url'], IdnToPunycodeFilterValidator::class],
            [['note', 'private_note'], 'string'],
            [['lobby_id'], 'exist',
                'targetClass' => Lobby2::class,
                'targetAttribute' => 'id',
            ],
            [['mode_id'], 'exist',
                'targetClass' => Mode2::class,
                'targetAttribute' => 'id',
            ],
            [['rule_id'], 'exist',
                'targetClass' => Rule2::class,
                'targetAttribute' => 'id',
            ],
            [['map_id'], 'exist',
                'targetClass' => Map2::class,
                'targetAttribute' => 'id',
            ],
            [['weapon_id'], 'exist',
                'targetClass' => Weapon2::class,
                'targetAttribute' => 'id',
            ],
            [['rank_id', 'rank_after_id'], 'exist',
                'targetClass' => Rank2::class,
                'targetAttribute' => 'id',
            ],
            [['link_url', 'note', 'private_note'], 'filter', 'filter' => function ($value) {
                $value = (string)$value;
                $value = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", $value);
                $value = preg_replace('/(?:\x0d\x0a|\x0d|\x0a){3,}/', "\n\n", $value);
                $value = trim($value);
                return $value === '' ? null : $value;
            },
            ],
            [['xMode'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lobby_id' => Yii::t('app', 'Lobby'),
            'mode_id' => Yii::t('app', 'Game Mode'),
            'rule_id' => Yii::t('app', 'Mode'),
            'map_id' => Yii::t('app', 'Stage'),
            'weapon_id' => Yii::t('app', 'Weapon'),
            'result' => Yii::t('app', 'Result'),
            'kill' => Yii::t('app', 'Kills'),
            'death' => Yii::t('app', 'Deaths'),
            'kill_or_assist' => Yii::t('app', 'Kill or Assist'),
            'special' => Yii::t('app', 'Specials'),
            'my_point' => Yii::t('app', 'Turf inked (including bonus)'),
            'link_url' => Yii::t('app', 'URL related to this battle'),
            'note' => Yii::t('app', 'Note (public)'),
            'private_note' => Yii::t('app', 'Note (private)'),

            'xMode' => Yii::t('app', 'Mode'),
        ];
    }

    public function createModeList(): array
    {
        $solo = Yii::t('app-rule2', '(Solo)');
        $twin = Yii::t('app-rule2', '(Twin)');
        $quad = Yii::t('app-rule2', '(Quad)');
        $priv = Yii::t('app-rule2', '(Private)');
        $nawabari = Yii::t('app-rule2', 'Turf War');
        $area = Yii::t('app-rule2', 'Splat Zones');
        $yagura = Yii::t('app-rule2', 'Tower Control');
        $hoko = Yii::t('app-rule2', 'Rainmaker');
        $asari = Yii::t('app-rule2', 'Clam Blitz');
        $private = Yii::t('app-rule2', 'Private Battle');
        return [
            '' => Yii::t('app', 'Unknown'),
            Yii::t('app-rule2', 'Regular Battle') => [
                'standard-regular-nawabari' => $nawabari,
            ],
            Yii::t('app-rule2', 'Ranked Battle') => [
                'standard-gachi-area' => "{$area} {$solo}",
                'standard-gachi-yagura' => "{$yagura} {$solo}",
                'standard-gachi-hoko' => "{$hoko} {$solo}",
                'standard-gachi-asari' => "{$asari} {$solo}",
            ],
            Yii::t('app-rule2', 'League Battle (Twin)') => [
                'squad_2-gachi-area' => "{$area} {$twin}",
                'squad_2-gachi-yagura' => "{$yagura} {$twin}",
                'squad_2-gachi-hoko' => "{$hoko} {$twin}",
                'squad_2-gachi-asari' => "{$asari} {$twin}",
            ],
            Yii::t('app-rule2', 'League Battle (Quad)') => [
                'squad_4-gachi-area' => "{$area} {$quad}",
                'squad_4-gachi-yagura' => "{$yagura} {$quad}",
                'squad_4-gachi-hoko' => "{$hoko} {$quad}",
                'squad_4-gachi-asari' => "{$asari} {$quad}",
            ],
            Yii::t('app-rule2', 'Splatfest') => [
                'fest_normal-fest-nawabari' => Yii::t('app-rule2', 'Splatfest (Normal)'),
                'standard-fest-nawabari' => Yii::t('app-rule2', 'Splatfest (Pro/Solo)'),
                'squad_4-fest-nawabari' => Yii::t('app-rule2', 'Splatfest (Team)'),
            ],
            $private => [
                'private-private-nawabari' => "{$nawabari} {$priv}",
                'private-private-area' => "{$area} {$priv}",
                'private-private-yagura' => "{$yagura} {$priv}",
                'private-private-hoko' => "{$hoko} {$priv}",
                'private-private-asari' => "{$asari} {$priv}",
            ],
        ];
    }

    public function getLobby(): ?Lobby2
    {
        return Lobby2::findOne(['id' => (int)$this->lobby_id]);
    }

    public function getMap(): ?Map2
    {
        return Map2::findOne(['id' => (int)$this->map_id]);
    }

    public function getMode(): ?Mode2
    {
        return Mode2::findOne(['id' => (int)$this->mode_id]);
    }

    public function getRule(): ?Rule2
    {
        return Rule2::findOne(['id' => (int)$this->rule_id]);
    }

    public function getXMode(): ?string
    {
        $rule = $this->getRule();
        $mode = $this->getMode();
        $lobby = $this->getLobby();
        if ($lobby && $lobby->key === 'private') {
            return $rule === null
                ? null
                : sprintf('private-private-%s', $rule->key);
        }

        if ($mode && $mode->key === 'fest') {
            if ($lobby) {
                switch ($lobby->key) {
                    case 'squad_4':
                        return 'squad_4-fest-nawabari';

                    case 'fest_normal':
                        return 'fest_normal-fest-nawabari';
                }
            }

            return 'standard-fest-nawabari';
        }

        if ($rule) {
            switch ($rule->key) {
                case 'nawabari':
                    return 'standard-regular-nawabari';

                case 'area':
                case 'yagura':
                case 'hoko':
                case 'asari':
                    return $lobby && ($lobby->key === 'squad_2' || $lobby->key === 'squad_4')
                        ? sprintf('%s-gachi-%s', $lobby->key, $rule->key)
                        : sprintf('standard-gachi-%s', $rule->key);

                default:
                    return null;
            }
        }
        return null;
    }

    public function setXMode(string $str): void
    {
        $this->lobby_id = null;
        $this->mode_id = null;
        $this->rule_id = null;

        $lobbies = '(standard|fest_normal|squad_[24]|private)';
        $modes = '(regular|gachi|fest|private)';
        $rules = '(nawabari|area|yagura|hoko|asari)';

        if (preg_match("/^{$lobbies}-{$modes}-{$rules}\$/", $str, $match)) {
            if ($_ = Lobby2::findOne(['key' => $match[1]])) {
                $this->lobby_id = (int)$_->id;
            }

            if ($_ = Mode2::findOne(['key' => $match[2]])) {
                $this->mode_id = (int)$_->id;
            }

            if ($_ = Rule2::findOne(['key' => $match[3]])) {
                $this->rule_id = (int)$_->id;
            }
        }
    }

    public function getIsWin(): ?bool
    {
        switch ($this->result) {
            case 'win':
                return true;

            case 'lose':
                return false;

            default:
                return null;
        }
    }

    public static function fromBattle(Battle2 $battle): self
    {
        $model = Yii::createObject([
            'class' => static::class,
        ]);
        foreach (array_keys($model->getAttributes()) as $key) {
            switch ($key) {
                case 'result':
                    $model->result = is_bool($battle->is_win)
                        ? ($battle->is_win ? 'win' : 'lose')
                        : null;
                    break;

                case 'xMode':
                    // 後で設定するのでスキップ
                    break;

                default:
                    $model->{$key} = $battle->{$key};
                    break;
            }
        }
        return $model;
    }
}
