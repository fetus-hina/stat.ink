<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "lobby2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 */
class Lobby2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lobby2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-rule', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->andWhere(['<>', 'key', 'squad_3'])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Lobby information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::getLobbyModeRuleMDTable(false),
                    ArrayHelper::getColumn($values, 'key', false)
                ),
                'name' => static::oapiRef(openapi\Name::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function getLobbyModeRuleMDTable(bool $isPostMode): string
    {
        $nawabari = ['nawabari'];
        $gachi = ['area', 'yagura', 'hoko', 'asari'];
        $list = [
            ['Regular', 'Solo Queue', 'standard', 'regular', $nawabari, ''],
            ['Regular', 'Join to friend', 'standard', 'regular', $nawabari, 'Same as Solo Queue'],
            ['Ranked', 'Solo Queue', 'standard', 'gachi', $gachi, ''],
            ['Ranked', 'League (Twin)', 'squad_2', 'gachi', $gachi, ''],
            ['Ranked', 'League (Quad)', 'squad_4', 'gachi', $gachi, ''],
            ['Splatfest (v4-)', 'Splatfest (Normal)', 'fest_normal', 'fest', $nawabari, ''],
            [
                'Splatfest (v4-)',
                'Splatfest (Pro)',
                'standard',
                'fest',
                $nawabari,
                $isPostMode
                    ? 'You can specify `fest_pro` instead of `standard` for convenience.'
                    : '',
            ],
            [
                'Splatfest (-v3)',
                'Splatfest (Solo)',
                'standard',
                'fest',
                $nawabari,
                $isPostMode ? 'deprecated' : '',
            ],
            [
                'Splatfest (-v3)',
                'Splatfest (Team)',
                'squad_4',
                'fest',
                $nawabari,
                $isPostMode ? 'deprecated' : '',
            ],
            [
                'Private Battle',
                'Private Battle',
                'private',
                'private',
                array_merge($nawabari, $gachi),
                '',
            ],
        ];
        
        return implode("\n", array_merge(
            [
                implode(' | ', [
                    Html::encode(Yii::t('app-apidoc2', 'Play Mode')),
                    '`lobby`',
                    '`mode`',
                    '`rule`',
                    Html::encode(Yii::t('app-apidoc2', 'Remarks')),
                ]),
                implode('|', array_map(
                    function (): string {
                        return '-';
                    },
                    range(1, 5)
                )),
            ],
            array_map(
                function (array $row): string {
                    return implode(' | ', [
                        vsprintf('%s<br>%s', [
                            Html::encode(Yii::t('app-apidoc2', $row[0])),
                            Html::encode(Yii::t('app-apidoc2', $row[1])),
                        ]),
                        sprintf('`%s`', $row[2]),
                        sprintf('`%s`', $row[3]),
                        implode(', ', array_map(
                            function (string $key): string {
                                return sprintf('`%s`', $key);
                            },
                            (array)$row[4]
                        )),
                        Html::encode(Yii::t('app-apidoc2', (string)($row[5] ?? ''))),
                    ]);
                },
                $list
            ),
        ));
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\Name::class,
        ];
    }

    public static function openApiExample(): array
    {
        return array_map(
            function (self $model): array {
                return $model->toJsonArray();
            },
            static::find()
                ->orderBy(['id' => SORT_ASC])
                ->where(['<>', 'key', 'squad_3'])
                ->all()
        );
    }
}
