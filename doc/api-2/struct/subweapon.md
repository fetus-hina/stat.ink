`subweapon`構造体
=================

`subweapon`構造体はある特定のサブウェポンを指し、次のような構造となっています。

```js
{
    "key": "quickbomb",
    "name": {
        "ja_JP": "クイックボム",
        "en_US": "Burst Bomb",
        "en_GB": "Burst Bomb",
        "es_ES": "Bomba rápida",
        "es_MX": "Bomba rápida"
    }
}
```

- `key` : 識別する時に使用するキーです。たとえば`GET /api/v2/weapon` APIで絞り込む際に指定し
ます。
- `name` : 名前を[`name`構造体](name.md)で表します。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
