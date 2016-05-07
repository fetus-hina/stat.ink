`weapon` 構造体
=============

`weapon` 構造体はある特定のブキを指し、次のような構造となっています。

```js
{
    "key": "wakaba",
    "type": {
        "key": "shooter",
        "name": {
            "en_US": "Shooters",
            "ja_JP": "シューター"
        }
    },
    "name": {
        "en_US": "Splattershot Jr.",
        "ja_JP": "わかばシューター"
    },
    "sub": {
        "key": "splashbomb",
        "name": {
            "en_US": "Splat Bomb",
            "ja_JP": "スプラッシュボム"
        }
    },
    "special": {
        "key": "barrier",
        "name": {
            "en_US": "Bubbler",
            "ja_JP": "バリア"
        }
    }
}
```

* `key` : 識別する時に使用するキーです。たとえば `POST /api/v1/battle` API でブキを指定する際に使用します。

* `type` : ブキの種類を [`weapon_type` 構造体](weapon_type.md) で表します。

* `name` : ブキの名前を [`name` 構造体](name.md) で表します。

* `sub` : サブウェポンを [`subweapon` 構造体](subweapon.md) で表します。

* `special` : スペシャルウェポンを [`special` 構造体](special.md) で表します。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
