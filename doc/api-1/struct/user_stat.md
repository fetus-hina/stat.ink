`user_stat` 構造体
==================

`user_stat` 構造体は次の構造になっていて、ユーザの簡易的な統計情報を示します。
（ウェブサイト上ではサイドバーに表示される情報です）

簡易統計はユーザがバトルを登録・編集・削除したタイミングで再集計されます。

```js
{
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
}
```

* `entire` : すべてのバトルの情報を使用した統計です。

* `nawabari`, `gachi` : レギュラーマッチ、ガチマッチの情報をそれぞれ使用した統計です。


`entire`
--------

```js
{
    "battle_count": 367,
    "wp": 61.6,
    "wp_24h": 70.6,
    "kill": 1397,
    "death": 1040,
    "kd_available_battle": 360
}
```

* `battle_count` : バトル数を示します。

* `wp` : 全体の勝率を百分率で示します。 `null` かもしれません。

* `wp_24h` : 集計時の最新 24 時間の勝率を百分率で示します。 `null` かもしれません。

* `kd_available_battle` : `kill`, `death` の集計対象になったバトル数を示します。 `battle_count` と一致するかもしれないししないかもしれません。 `kill`, `death` をこの数値で割れば試合ごとの平均が出ます。

* `kill` : `kd_available_battle` 試合中の敵イカを殺した数の合計を示します。

* `death` : `kd_available_battle` 試合中の殺された数または自殺した数の合計を示します。

※ `battle_count` を除き、プライベートマッチが集計されていません。仕様です。


`nawabari` `gachi`
------------------

```js
{
    "battle_count": 359,
    "wp": 61.8,
    "kill": 1370,
    "death": 1005
}
```

概ね `entire` と同じですが、現時点では `kd_available_battle` に相当するものと `wp_24h` に相当するものがありません。


----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
