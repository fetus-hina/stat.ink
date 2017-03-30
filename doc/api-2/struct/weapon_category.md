`weapon_category`構造体
=======================

`weapon_category`構造体はブキの種類（シューター、チャージャー、…）を指し、次のような構造となっています。

```js
{
    "key": "shooter",
    "name": {
        "ja_JP": "シューター",
        "en_US": "Shooters",
        "en_GB": "Shooters",
        "es_ES": "Lanzatintas",
        "es_MX": "Rociadors"
    }
}
```

- `key` : 識別する時に使用するキーです。たとえば`GET /api/v1/weapon` APIで絞り込む際に指定し
ます。
- `name` : ブキの名前を [`name` 構造体](name.md) で表します。


v1との差異
----------

- この構造体と同等の内容は`weapon_type`でした。（v2の[`weapon_type`](weapon_type.md)相当の構造がありませんでした）

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
