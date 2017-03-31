`GET /api/v2/rule`
==================

| | |
|-|-|
|URL|`https://stat.ink/api/v2/rule`|
|Return-Type|`application/json`|
|認証|なし|

プレイモード、ルールの一覧をJSON形式、[`mode`構造体](struct/weapon.md)の配列で返します。
各ブキの`key`が他のAPIで利用するときの値です。

出現順に規定はありません。（利用者側で適切に並び替えてください）

応答の構造体は`mode`（レギュラーマッチ、ガチマッチ）であって`rule`でないことに注意してください。

クエリパラメータ
----------------

現在未実装です。


出力例
------

```js
[
    {
        "key": "regular",
        "name": {
            "ja_JP": "レギュラーバトル",
            "en_US": "Regular Battle",
            "en_GB": "Regular Battle",
            "es_ES": "Combate amistoso",
            "es_MX": "Combate amistoso"
        },
        "rules": [
            {
                "key": "nawabari",
                "name": {
                    "ja_JP": "ナワバリバトル",
                    "en_US": "Turf War",
                    "en_GB": "Turf War",
                    "es_ES": "Territorial",
                    "es_MX": "Territorial"
                }
            }
        ]
    }
    // ...
]
```

v1との差異
----------

- v1では応答は`rule`の配列で、その内部で`mode`の区別を示していました。v2では入れ替わっています。
    - これは、例えばガチナワバリに対応するための変更です。API制定時点でこの配慮が必要かはわかっていません。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
