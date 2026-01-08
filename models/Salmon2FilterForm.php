<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Throwable;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\db\Now;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_keys;
use function array_map;
use function explode;
use function implode;
use function in_array;
use function preg_match;
use function preg_quote;
use function sprintf;
use function strpos;
use function substr;
use function time;
use function trim;
use function version_compare;

use const SORT_DESC;

class Salmon2FilterForm extends Model
{
    public $user;

    public $stage;
    public $special;
    public $result;
    public $reason;
    public $term;
    public $filter;

    private $filterRotation;
    private $versions;

    public function formName()
    {
        return 'filter';
    }

    public function init()
    {
        parent::init();
        $this->versions = $this->initVersions();
    }

    public function rules()
    {
        return [
            [['stage', 'special', 'result', 'reason', 'term', 'filter'], 'string'],
            [['stage'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => 'key',
            ],
            [['special'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSpecial2::class,
                'targetAttribute' => 'key',
            ],
            [['result'], 'in',
                'range' => [
                    'cleared',
                    'failed',
                    'failed-wave3',
                    'failed-wave2',
                    'failed-wave1',
                ],
            ],
            [['reason'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonFailReason2::class,
                'targetAttribute' => 'key',
            ],
            [['term'], 'match',
                'pattern' => sprintf('/^(?:(?:%s))$/', implode(')|(?:', ArrayHelper::toFlatten([
                    '\d{4}-\d{2}', // YYYY-MM
                    array_map(
                        fn (string $fixedPattern): string => preg_quote($fixedPattern, '/'),
                        ArrayHelper::toFlatten([
                            ['this-rotation', 'prev-rotation'],
                            array_keys($this->getValidVersions()),
                        ]),
                    ),
                ]))),
            ],
            [['filter'], 'validateFilter', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'stage' => Yii::t('app', 'Stage'),
            'special' => Yii::t('app', 'Special'),
            'result' => Yii::t('app', 'Result'),
            'reason' => Yii::t('app', 'Fail Reason'),
            'term' => Yii::t('app', 'Period'),
            'filter' => Yii::t('app', 'Filter'),
        ];
    }

    public function validateFilter(string $attr, $params): void
    {
        $value = trim((string)($this->$attr));
        if ($value === '' || $this->hasErrors($attr)) {
            return;
        }

        try {
            foreach (explode(' ', $value) as $v) {
                $v = trim($v);
                if ($v === '') {
                    continue;
                }

                $pos = strpos($v, ':');
                if ($pos === false || $pos < 1) {
                    throw new Exception();
                }

                switch (substr($v, 0, $pos)) {
                    case 'rotation':
                        $v = substr($v, $pos + 1);
                        if (preg_match('/^\d+$/', $v)) {
                            $this->filterRotation = [(int)$v, (int)$v];
                        } elseif (preg_match('/^(\d+)-(\d+)$/', $v, $match)) {
                            $this->filterRotation = [
                                (int)$match[1],
                                (int)$match[2],
                            ];
                        } else {
                            throw new Exception();
                        }
                        return;

                    default:
                        throw new Exception();
                }
            }
        } catch (Throwable $e) {
            $this->addError($attr, Yii::t('yii', '{attribute} is invalid.', [
                'attribute' => $this->getAttributeLabel($attr),
            ]));
        }
    }

    public function decorateQuery(ActiveQuery $query): ActiveQuery
    {
        if (!$this->validate()) {
            $query->andWhere('0 = 1');
            return $query;
        }

        if ($this->stage) {
            $stage = SalmonMap2::findOne(['key' => $this->stage]);
            $query->andWhere(['{{salmon2}}.[[stage_id]]' => $stage->id]);
        }

        if ($this->special) {
            $special = SalmonSpecial2::findOne(['key' => $this->special]);
            $query
                ->innerJoin(
                    'salmon_player2',
                    implode(' AND ', [
                        '{{salmon2}}.[[id]] = {{salmon_player2}}.[[work_id]]',
                        '{{salmon_player2}}.[[is_me]] = TRUE',
                    ]),
                )
                ->andWhere([
                    '{{salmon_player2}}.[[special_id]]' => $special->id,
                ]);
        }

        if ($this->result) {
            switch ($this->result) {
                case 'cleared':
                    $query->andWhere(['{{salmon2}}.[[clear_waves]]' => 3]);
                    break;

                case 'failed':
                    $query->andWhere(['<', '{{salmon2}}.[[clear_waves]]', 3]);
                    break;

                case 'failed-wave3':
                    $query->andWhere(['{{salmon2}}.[[clear_waves]]' => 2]);
                    break;

                case 'failed-wave2':
                    $query->andWhere(['{{salmon2}}.[[clear_waves]]' => 1]);
                    break;

                case 'failed-wave1':
                    $query->andWhere(['{{salmon2}}.[[clear_waves]]' => 0]);
                    break;
            }
        }

        if ($this->reason) {
            $reason = SalmonFailReason2::findOne(['key' => $this->reason]);
            $query->andWhere(['{{salmon2}}.[[fail_reason_id]]' => $reason->id]);
        }

        if ($this->term) {
            if ($this->term === 'this-rotation' || $this->term === 'prev-rotation') {
                if ($schedule = $this->getSchedule()) {
                    $query->andWhere(['{{salmon2}}.[[shift_period]]' => $schedule->period]);
                } else {
                    $query->andWhere('0 = 1');
                }
            } elseif (substr($this->term, 0, 1) === 'v') {
                $vID = substr($this->term, 1);
                if (isset($this->versions[$vID])) {
                    [$date1, $date2] = $this->versions[$vID]->getAvailableDateRange();

                    $query->andWhere([
                        '>=',
                        '{{salmon2}}.[[shift_period]]',
                        BattleHelper::calcPeriod2($date1->getTimestamp()),
                    ]);

                    if ($date2) {
                        $query->andWhere([
                            '<',
                            '{{salmon2}}.[[shift_period]]',
                            BattleHelper::calcPeriod2($date2->getTimestamp()),
                        ]);
                    }
                } else {
                    $query->andWhere('0 = 1');
                }
            } elseif (preg_match('/^(\d{4})-(\d{2})$/', $this->term, $match)) {
                $now = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()));
                $year = (int)$match[1];
                $month = (int)$match[2];
                if (
                    (
                        2018 <= $year && $year < (int)$now->format('Y') &&
                        (1 <= $month && $month <= 12)
                    ) || (
                        ($year === (int)$now->format('Y')) &&
                        (1 <= $month && $month <= (int)$now->format('n'))
                    )
                ) {
                    // 開始日時基準で検索する
                    $lowerLimit = (new DateTimeImmutable())
                        ->setTimezone(new DateTimeZone('Etc/UTC'))
                        ->setTime(0, 0, 0)
                        ->setDate($year, $month, 1);
                    $upperLimit = $lowerLimit->add(new DateInterval('P1M'));
                    $query->andWhere([
                        'BETWEEN',
                        '{{salmon2}}.[[shift_period]]',
                        BattleHelper::calcPeriod2($lowerLimit->getTimestamp()),
                        BattleHelper::calcPeriod2($upperLimit->getTimestamp() - 1),
                    ]);
                } else {
                    $query->andWhere('0 = 1');
                }
            } else {
                $query->andWhere('0 = 1');
            }
        }

        if ($this->filterRotation) {
            $query->andWhere(['BETWEEN', '{{salmon2}}.[[shift_period]]',
                $this->filterRotation[0],
                $this->filterRotation[1],
            ]);
        }

        return $query;
    }

    public function toPermalinkParams(): array
    {
        $results = [];

        if (!$this->validate()) {
            return $this->attributes;
        }

        // 特殊な属性以外をそのままコピー
        $filterAttributes = [
            'term',
        ];
        foreach ($this->attributes as $attr => $value) {
            if (in_array($attr, $filterAttributes, true)) {
                continue;
            }

            $value = trim((string)$value);
            if ($value !== '') {
                $results[$attr] = $value;
            }
        }

        if ($this->term) {
            if ($this->term === 'this-rotation' || $this->term === 'prev-rotation') {
                if ($schedule = $this->getSchedule()) {
                    $results['filter'] = sprintf('rotation:%d', $schedule->period);
                }
            }
        }

        return $results;
    }

    private function getSchedule(): ?SalmonSchedule2
    {
        if ($this->term !== 'this-rotation' && $this->term !== 'prev-rotation') {
            return null;
        }

        return SalmonSchedule2::find()
            ->nowOrPast()
            ->newerFirst()
            ->offset($this->term === 'this-rotation' ? 0 : 1)
            ->limit(1)
            ->one();
    }

    public function getValidVersions(): array
    {
        return ArrayHelper::map(
            $this->versions,
            fn (SplatoonVersionGroup2 $v): string => 'v' . $v->tag,
            fn (SplatoonVersionGroup2 $v): string => Yii::t('app', 'Version {0}', [
                Yii::t('app-version2', $v->name),
            ]),
        );
    }

    private function initVersions(): array
    {
        $versionGroupIds = array_filter(
            ArrayHelper::getColumn(
                SplatoonVersion2::find()
                    ->andWhere(['and',
                        ['<=', 'released_at', new Now()],
                    ])
                    ->asArray()
                    ->all(),
                fn (array $row): ?int => version_compare('4.0.0', $row['tag'], '<=')
                        ? (int)$row['group_id']
                        : null,
            ),
            fn (?int $value): bool => $value !== null,
        );

        return ArrayHelper::map(
            SplatoonVersionGroup2::find()
                ->andWhere(['id' => $versionGroupIds])
                ->orderBy(['tag' => SORT_DESC])
                ->all(),
            'tag',
            fn (SplatoonVersionGroup2 $row): SplatoonVersionGroup2 => $row,
        );
    }
}
