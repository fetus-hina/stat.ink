`GET /api/v2/gear.csv`
========================

| | |
|-|-|
|URL|`https://stat.ink/api/v2/gear.csv`|
|Return-Type|`text/csv`|
|認証|なし|

[JSON version](get-gear.md)

ブキの一覧をCSV形式(RFC 4180)で返します。

1行目はヘッダです。ギアの出現順に既定はありません。（利用者側で適切に並び替えてください）

このAPIに直接ブラウザでアクセスすると、CSVファイルが（インライン表示ではなく）ダウンロードされます。

このAPIが返すのと同等のページがあります。

  - [アタマ](https://stat.ink/api-info/gear2-headgear)
  - [フク](https://stat.ink/api-info/gear2-clothing)
  - [クツ](https://stat.ink/api-info/gear2-shoes)


クエリパラメータ (Query Parameters)
-----------------------------------

|パラメータ名<br>Param. Name|型<br>Type|例<br>Example|内容|
|---------------------------|----------|-------------|----|
|`type`|string (`headgear`, `clothing` or `shoes`)|`headgear`|ギアの種類を指定します|
|`brand`|string|`krak_on`|ギアのブランドを指定します|
|`ability`|string|`special_power_up`|ギア標準のメインギアパワーを指定します|


カラム (Columns)
----------------

出力カラムは次の順に並びます。念のため、1行目のヘッダを確認することをおすすめします。

|カラム名|出力例|内容|
|-|-|-|
|`type`|`headgear`|ギアの種類を示す既定の文字列|
|`brand`|`rockenberg`|ブランドを示す既定の文字列|
|`key`|`18k_aviators`|ギアを特定するための既定の文字列。ほかのAPIでブキを指定する際に利用する値|
|`splatnet`|`3008`|イカリング2で利用されているID|
|`primary_ability`|`last_ditch_effort`|ギアの標準のメインギアパワーを示す既定の文字列|
|`[` 言語コード `]`|`18K Aviators`|各言語でのギアの名称|

言語（コード）の出現順や数は不定だと考えてください。
対応言語が増加する際にどこにいくつ追加されるかはわかりません。

また、新しい項目を出力する必要が生じた場合は、最後の項目の後ろに追加される可能性があります

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
