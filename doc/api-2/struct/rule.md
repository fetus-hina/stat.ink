`rule`構造体
===============

`rule`構造体はある特定のルールを指し、次のような構造となっています。

```js
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
```

- `key` : 識別する時に使用するキーです。たとえば`POST /api/v2/battle`APIで指定します。
- `name` : 名前を[`name` 構造体](name.md)で表します。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
