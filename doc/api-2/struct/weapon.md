`weapon`構造体
=============

`weapon`構造体はある特定のブキを指し、次のような構造となっています。

```js
{
    "key": "sshooter",
    "splatnet": 40,
    "type": {
        "key": "shooter",
        "name": {
            "ja_JP": "シューター",
            "en_US": "Shooters",
            "en_GB": "Shooters",
            "es_ES": "Lanzatintas",
            "es_MX": "Rociadors"
        },
        "category": {
            "key": "shooter",
            "name": {
                "ja_JP": "シューター",
                "en_US": "Shooters",
                "en_GB": "Shooters",
                "es_ES": "Lanzatintas",
                "es_MX": "Rociadors"
            }
        }
    },
    "name": {
        "ja_JP": "スプラシューター",
        "en_US": "Splattershot",
        "en_GB": "Splattershot",
        "es_ES": "Lanzatintas",
        "es_MX": "Rociador"
    },
    "sub": {
        "key": "quickbomb",
        "name": {
            "ja_JP": "クイックボム",
            "en_US": "Burst Bomb",
            "en_GB": "Burst Bomb",
            "es_ES": "Bomba rápida",
            "es_MX": "Bomba rápida"
        }
    },
    "special": {
        "key": "missile",
        "name": {
            "ja_JP": "マルチミサイル",
            "en_US": "Tenta Missiles",
            "en_GB": "Tenta Missiles",
            "es_ES": "Lanzamisiles",
            "es_MX": "Lanzamisiles"
        }
    },
    "reskin_of": null,
    "main_ref": "sshooter"
}
```

- `key` : 識別する時に使用するキーです。たとえば`POST /api/v2/battle` APIでブキを指定する際に使用します。
- `splatnet` : イカリング2で使用されていると思われるキーです。
- `type` : ブキの種類を[`weapon_type`構造体](weapon_type.md)で表します。ここでいう「種類」は、シューター、ブラスター、ローラー、フデなどの大雑把な区分です。（シューター、チャージャー...という大きな区分は更にその中の`category`が表しています）
- `name` : ブキの名前を[`name`構造体](name.md)で表します。
- `sub` : サブウェポンを[`subweapon`構造体](subweapon.md)で表します。
- `special` : スペシャルウェポンを[`special`構造体](special.md)で表します。
- `reskin_of` : ブキ構成が全く同じで名前（と見た目）だけ異なるブキがあるとき（Splatoon 1のヒーローシリーズやオクタシューターなど）、その本来のブキの`key`が、そうでないとき`null`が設定されます。
- `main_ref` : メインウェポンが同じ代表ブキの`key`を示します。`reskin_of`と違い、常に値が設定されます。


v1との差異
----------

- `reskin_of`, `main_ref`, `splatnet` が追加されています。
- `type`の示す内容が変更されています。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
