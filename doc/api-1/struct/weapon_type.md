`weapon_type` 構造体
====================

`weapon_type` 構造体はブキの種類（シューター、チャージャー、…）を指し、次のような構造となっています。

```js
{
    "key": "shooter",
    "name": {
        "en_US": "Shooters",
        "ja_JP": "シューター"
    }
}
```

* `key` : 識別する時に使用するキーです。たとえば `GET /api/v1/weapon` API で絞り込む際に指定します。

* `name` : ブキの名前を [`name` 構造体](name.md) で表します。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
