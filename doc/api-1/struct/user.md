`user` 構造体
=============

`user` 構造体は次の構造になっています。

```js
{
    "id": 1,
    "name": "ひな",
    "screen_name": "fetus_hina",
    "url": "https://stat.ink/u/fetus_hina",
    "join_at": {
        "time": 1443175797,
        "iso8601": "2015-09-25T10:09:57+00:00"
    },
    "stat": {
        "entire": {
            "battle_count": 367,
            "wp": 61.6,
            "wp_24h": 70.6,
            "kill": 1397,
            "death": 1040,
            "kd_available_battle": 360
        },
        "nawabari": {
            "battle_count": 359,
            "wp": 61.8,
            "kill": 1370,
            "death": 1005
        },
        "gachi": {
            "battle_count": 5,
            "wp": 20,
            "kill": 18,
            "death": 32
        }
    },
    "latest_battle": null
}
```

* `id` : 内部ユーザID。実際には使用することはありませんが、アプリケーションは unique key として扱うことが出来ます。

* `name` : ユーザの指定した表示用の名前。変更される可能性があります。

* `screen_name` : ユーザの指定したログイン用・URL用の名称。API でユーザを特定する際にはこれを指定します。現在のところこれをユーザが変更することはできませんが、変更される可能性があるものと取り扱うことをおすすめします。

* `url` : このURLにアクセスすることでユーザの情報が表示されることを示します。

* `join_at` : サイトへの登録日時を [`time` 構造体](time.md) で表します。

* `stat` : ユーザの簡易統計情報を [`user_stat` 構造体](user_stat.md) で表します。

* `latest_battle` : 一部の API で、最新のバトル情報を [`battle` 構造体](battle.md) で表します。キーが存在しないかもしれません。また、値が null かもしれません。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
