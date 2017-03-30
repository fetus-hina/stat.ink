`weapon_type`構造体
===================

`weapon_type` 構造体はブキの大雑把な分類（シューター、ブラスター、ローラー、フデ…）を指し、次のような構造となっています。

```js
{
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
}
```

- `key` : 識別する時に使用するキーです。たとえば`GET /api/v2/weapon` APIで絞り込む際に指定し
ます。
- `name` : 分類名を[`name`構造体](name.md)で表します。
- `category` : 詳細な分類（シューター、チャージャー、ローラー…）を[`weapon_category`構造体](weapon_category.md)で表します。


v1との差異
----------

- この構造に相当する内容はなく、シューターなら一括でシューターに分類されていました。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
