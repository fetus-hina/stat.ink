<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\base\Model;

use function array_keys;
use function array_merge;
use function array_reverse;
use function array_shift;
use function count;
use function sprintf;
use function time;
use function usort;
use function version_compare;

use const SORT_DESC;

class EntireWeapon2Form extends Model
{
    public $term;
    public $map;

    public function init()
    {
        parent::init();
        $this->term = $this->getDefaultTerm();
    }

    private function getDefaultTerm(): string
    {
        $latest = SplatoonVersion2::find()
            ->orderBy(['released_at' => SORT_DESC])
            ->limit(1)
            ->one();
        return $latest !== null ? 'v' . $latest->tag : '';
    }

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['term', 'map'], 'string'],
            [['term'], 'in',
                'range' => array_keys($this->getTermList()),
            ],
            [['map'], 'exist', 'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => 'key',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'term' => 'Term',
            'map' => 'Map',
        ];
    }

    public function toQueryParams($formName = false)
    {
        if ($formName === false) {
            $formName = $this->formName();
        }

        $ret = [];
        $push = function (string $key, $value) use ($formName, &$ret) {
            if ($formName != '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };
        foreach ($this->attributes as $key => $value) {
            $push($key, $value);
        }
        return $ret;
    }

    public function getTermList(): array
    {
        static $list;
        if (!$list) {
            $list = array_merge(
                ['' => Yii::t('app', 'Any Time')],
                $this->getVersionList(),
                $this->getMonthList(),
            );
        }
        return $list;
    }

    private function getVersionList(): array
    {
        $result = [];
        $groups = SplatoonVersionGroup2::find()->with('versions')->asArray()->all();
        usort($groups, fn (array $a, array $b): int => version_compare($b['tag'], $a['tag']));
        foreach ($groups as $group) {
            switch (count($group['versions'])) {
                case 0:
                    break;

                case 1:
                    $version = array_shift($group['versions']);
                    $result['v' . $version['tag']] = Yii::t('app', 'Version {0}', [
                        Yii::t('app-version2', $version['name']),
                    ]);
                    break;

                default:
                    $result['~v' . $group['tag']] = Yii::t('app', 'Version {0}', [
                        Yii::t('app-version2', $group['name']),
                    ]);
                    usort($group['versions'], fn (array $a, array $b): int => version_compare($b['tag'], $a['tag']));
                    $n = count($group['versions']);
                    foreach ($group['versions'] as $i => $version) {
                        $result['v' . $version['tag']] = sprintf(
                            '%s %s',
                            $i === $n - 1 ? '┗' : '┣',
                            Yii::t('app', 'Version {0}', [
                                Yii::t('app-version2', $version['name']),
                            ]),
                        );
                    }
                    break;
            }
        }
        return $result;
    }

    private function getMonthList(): array
    {
        $interval = new DateInterval('P1M');
        $date = new DateTimeImmutable()
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setDate(2017, 7, 1)
            ->setTime(0, 0, 0);
        $limit = new DateTimeImmutable()
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
        // The keys are UTC year-months (matching stat_weapon2_use_count_per_month.year_month),
        // so the display labels must be rendered in UTC too — otherwise a user-local timezone
        // west of UTC would shift midnight back into the previous month.
        $formatter = clone Yii::$app->formatter;
        $formatter->timeZone = 'Etc/UTC';
        $result = [];
        for (; $date <= $limit; $date = $date->add($interval)) {
            $result[$date->format('Y-m')] = $formatter->asDate($date, Yii::t('app', 'MMMM y'));
        }
        return array_reverse($result, true);
    }
}
