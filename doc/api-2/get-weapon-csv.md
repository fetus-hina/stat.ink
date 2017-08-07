`GET /api/v2/weapon.csv`
========================

| | |
|-|-|
|URL|`https://stat.ink/api/v2/weapon.csv`|
|Return-Type|`text/csv`|
|認証|なし|

[JSON version](get-weapon.md)

ブキの一覧をCSV形式(RFC 4180)で返します。

1行目はヘッダです。ブキの出現順に既定はありません。（利用者側で適切に並び替えてください）

このAPIに直接ブラウザでアクセスすると、CSVファイルが（インライン表示ではなく）ダウンロードされます。

[このAPIが返すのと同等のページがあります。](https://stat.ink/api-info/weapon2)


クエリパラメータ
----------------

現在未実装です。


カラム
------

出力カラムは次の順に並びます。念のため、1行目のヘッダを確認することをおすすめします。

|カラム名|出力例|内容|
|-|-|-|
|`category1`|`shooter`|ブキのカテゴリー（広い範囲）を示す既定の文字列|
|`category2`|`maneuver`|ブキのカテゴリー（狭い範囲）を示す既定の文字列|
|`key`|`maneuver_collabo`|ブキを特定するための既定の文字列。ほかのAPIでブキを指定する際に利用する値|
|`subweapon`|`curlingbomb`|サブウェポンを特定するための既定の文字列|
|`special`|`jetpack`|スペシャルウェポンを特定するための既定の文字列|
|`mainweapon`|`maneuver`|メインウェポンを特定するための規定の文字列。`key`に現れます|
|`reskin`|`maneuver_collabo`|見た目だけが違うブキ（例えばヒーローなんとかレプリカ）で正規化に利用できる文字列。`key`に現れます|
|`[` 言語コード `]`|`Enperry Splat Dualies`|各言語でのブキの名称|

言語（コード）の出現順や数は不定だと考えてください。
対応言語が増加する際にどこにいくつ追加されるかはわかりません。

また、新しい項目を出力する必要が生じた場合は、`reskin`の後ろに追加される可能性があります


----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
