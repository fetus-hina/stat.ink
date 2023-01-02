<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m220820_015712_weapon3_copy extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upMainWeapon();
        $this->upWeapon();
        $this->upAlias();

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%mainweapon3}}',
            '{{%weapon3}}',
            '{{%weapon3_alias}}',
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%weapon3_alias}}');
        $this->delete('{{%weapon3}}');
        $this->delete('{{%mainweapon3}}');

        return true;
    }

    private function upMainWeapon(): void
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $select = (new Query())
            ->select([
                'key',
                'type_id' => new Expression($this->buildWeaponTypeMapQuery()),
                'name',
            ])
            ->from('{{%weapon2}}')
            ->andWhere('{{%weapon2}}.[[id]] = {{%weapon2}}.[[canonical_id]]')
            ->andWhere('{{%weapon2}}.[[splatnet]] % 10 = 0')
            ->orderBy([
                $this->buildWeaponTypeMapQuery() => SORT_ASC,
                '{{%weapon2}}.[[splatnet]]' => SORT_ASC,
                '{{%weapon2}}.[[id]]' => SORT_ASC,
            ]);

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName('{{%mainweapon3}}'),
            implode(', ', array_map(
                fn (string $columnName): string => $db->quoteColumnName($columnName),
                ['key', 'type_id', 'name'],
            )),
            $select->createCommand()->rawSql,
        ]);
        $this->execute($sql);
    }

    private function buildWeaponTypeMapQuery(): string
    {
        // key:   splatoon 2 key
        // value: splatoon 3 key
        $versionMap = [
            'blaster' => 'blaster',
            'brella' => 'brella',
            'brush' => 'brush',
            'charger' => 'charger',
            'maneuver' => 'maneuver',
            'reelgun' => 'reelgun',
            'roller' => 'roller',
            'shooter' => 'shooter',
            'slosher' => 'slosher',
            'splatling' => 'spinner',
        ];

        $db = $this->db;
        assert($db instanceof Connection);

        return vsprintf('(CASE %s %s END)', [
            vsprintf('%s.%s', [
                $db->quoteTableName('weapon2'),
                $db->quoteColumnName('type_id'),
            ]),
            implode(' ', array_map(
                fn (string $s2key, string $s3key): string => vsprintf('WHEN %d THEN %d', [
                        $this->key2id('{{%weapon_type2}}', $s2key),
                        $this->key2id('{{%weapon_type3}}', $s3key),
                    ]),
                array_keys($versionMap),
                array_values($versionMap),
            )),
        ]);
    }

    private function upWeapon(): void
    {
        $db = $this->db;
        assert($db instanceof Connection);

        // ローンチ時点では、Splatoon 2 の「基本となるメインウェポン」と少数の追加ブキが使用可能
        // upMainWeapon で「基本となるメインウェポン」を mainweapon3 にコピーしてあるので
        // 現時点では mainweapon3 に入っているデータをそのままコピーすれば OK
        // 追加ブキは次の migration に任せる
        //
        // 本来は subweapon_id や special_id も設定しなければならないが、
        // migraton 作成時点で完全な対応リストが存在しないので一旦全ブキ null のまま作成する

        $select = (new Query())
            ->select([
                'key',
                'mainweapon_id' => '{{%mainweapon3}}.[[id]]',
                'name',
            ])
            ->from('{{%mainweapon3}}')
            ->orderBy(['{{%mainweapon3}}.[[id]]' => SORT_ASC]);

        $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
            $db->quoteTableName('{{%weapon3}}'),
            implode(', ', array_map(
                fn (string $columnName): string => $db->quoteColumnName($columnName),
                ['key', 'mainweapon_id', 'name'],
            )),
            $select->createCommand()->rawSql,
        ]);
        $this->execute($sql);
    }

    private function upAlias(): void
    {
        /**
         * @phpstan-var array{id: int|numeric-string, key: string, name: string}[]
         */
        $weapons = (new Query())
            ->select(['id', 'key', 'name'])
            ->from('{{%weapon3}}')
            ->orderBy(['{{%weapon3}}.[[id]]' => SORT_ASC])
            ->all();

        /**
         * @var string[]
         */
        $existKeys = ArrayHelper::getColumn($weapons, 'key');

        /**
         * @phpstan-var array{int, string}
         */
        $inserts = [];
        foreach ($weapons as $w) {
            $key = self::name2key3($w['name']);
            if (!in_array($key, $existKeys, true)) {
                $inserts[] = [
                    (int)$w['id'],
                    $key,
                ];
                $existKeys[] = $key;
            }
        }

        if ($inserts) {
            $this->batchInsert('{{%weapon3_alias}}', ['weapon_id', 'key'], $inserts);
        }
    }
}
