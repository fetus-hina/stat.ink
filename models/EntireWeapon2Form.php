<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\models\Map2;
use yii\base\Model;

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
            [['term'], 'required'],
            [['term', 'map'], 'string'],
            [['term'], 'in',
                'range' => array_keys($this->getTermList()),
            ],
            [['map'], 'exist',
                'skipOnError' => true,
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

    private $cachedTermList;
    public function getTermList(): array
    {
        if (!$this->cachedTermList) {
            $this->cachedTermList = array_merge(
                [
                    '*' => Yii::t('app', 'Any Time'),
                ],
                $this->getVersionList(),
            );
        }

        return $this->cachedTermList;
    }

    private function getVersionList(): array
    {
        $result = [];
        $groups = SplatoonVersionGroup2::find()->with('versions')->asArray()->all();
        usort($groups, function (array $a, array $b): int {
            return version_compare($b['tag'], $a['tag']);
        });
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
                    usort($group['versions'], function (array $a, array $b): int {
                        return version_compare($b['tag'], $a['tag']);
                    });
                    $n = count($group['versions']);
                    foreach ($group['versions'] as $i => $version) {
                        $result['v' . $version['tag']] = sprintf(
                            '%s %s',
                            $i === $n - 1 ? '┗' : '┣',
                            Yii::t('app', 'Version {0}', [
                                Yii::t('app-version2', $version['name']),
                            ])
                        );
                    }
                    break;
            }
        }
        return $result;
    }

    public function updateToDefault(): self
    {
        $this->term = vsprintf('~v%s', [
            $this->getLatestVersionGroup()->tag,
        ]);
        $this->map = '';
        $this->clearErrors();
        return $this;
    }

    private function getLatestVersionGroup(): SplatoonVersionGroup2
    {
        return SplatoonVersion2::findCurrentVersion()->group;
    }
}
