`GET /api/v2/weapon`
====================

| | |
|-|-|
|URL|`https://stat.ink/api/v2/weapon`|
|Return-Type|`application/json`|
|認証|なし|

ブキの一覧をJSON形式、[`weapon`構造体](struct/weapon.md)の配列で返します。
各ブキの`key`が他のAPIで利用するときの値です。

出現順に規定はありません。（利用者側で適切に並び替えてください）

クエリパラメータ
----------------

現在未実装です。


出力例
------

```js
[
    {
        "key": "sshooter",
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
        }
        "reskin_of": null,
        "main_ref": "sshooter"
    },
    // ...
]
```

v1との差異
----------

- `weapon`構造体にメンバーが増えています。
- `weapon`構造体中の`type`の表す内容が変更されています。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
