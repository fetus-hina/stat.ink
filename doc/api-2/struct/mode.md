`mode`構造体
===========

`mode`構造体はある特定のゲームモード（レギュラーバトル、ガチマッチ）を差し、次のような構造となっています。

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
    },
    // ...
]
```

- `key` : 識別する時に使用するキーです。たとえば`POST /api/v2/battle` APIでゲームモードを指定する際に使用します。
- `name` : マップの名前を[`name`構造体](name.md)で表します。
- `rules` : このモードで有効なルールを[`rule`構造体](rule.md)で表します。

v1との差異
----------

- v1では`rule`の中に`mode`が示される形で応答されていました。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
