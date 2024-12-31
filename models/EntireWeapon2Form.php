<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
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

class EntireWeapon2Form extends Model
{
    public $term;
    public $map;

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
        $date = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setDate(2017, 7, 1)
            ->setTime(0, 0, 0);
        $limit = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
        $formatter = Yii::$app->formatter;
        $result = [];
        for (; $date <= $limit; $date = $date->add($interval)) {
            $result[$date->format('Y-m')] = $formatter->asDate($date, Yii::t('app', 'MMMM y'));
        }
        return array_reverse($result, true);
    }
}
