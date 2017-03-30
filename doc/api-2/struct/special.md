`special`構造体
===============

`special`構造体はある特定のスペシャルウェポンを指し、次のような構造となっています。

```js
{
    "key": "missile",
    "name": {
        "ja_JP": "マルチミサイル",
        "en_US": "Tenta Missiles",
        "en_GB": "Tenta Missiles",
        "es_ES": "Lanzamisiles",
        "es_MX": "Lanzamisiles"
    }
}
```

- `key` : 識別する時に使用するキーです。たとえば`GET /api/v2/weapon` APIで絞り込む際に指定し
ます。
- `name` : 名前を[`name` 構造体](name.md)で表します。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
