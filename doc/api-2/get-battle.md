`GET /api/v2/battle`
===================

| | |
|-|-|
|URL|`https://stat.ink/api/v2/battle`|
|Return-Type|`application/json`|
|認証|なし|

バトル情報の一覧をJSON形式、[`battle`構造体](struct/battle.md)の配列で返します。

このAPIは結構重いのでほどほどの頻度で叩いてください。


クエリパラメータ
----------------

|パラメータ名|型|内容|
|------------|--|----|
|`screen_name`|文字列|指定されたユーザのデータのみを検索します。指定しなければすべてのユーザを検索します。|
|`newer_than`|数値|指定されたバトルIDよりも新しいバトルを検索します。指定した数値そのものは含みません。|
|`older_than`|数値|指定されたバトルIDよりも古いバトルを検索します。指定した数値そのものは含みません。|
|`count`|数値(1～50)|指定した個数を上限に検索します。|
|`order`|`asc` or `desc`|`asc`を指定すると古い順に表示します。`desc`を指定するか無指定だと新しい順に表示します。|

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
