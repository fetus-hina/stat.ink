`GET /api/v2/battle`, `GET /api/v2/user-battle`
===============================================

| |`/api/v2/battle`|`/api/v2/user-battle`|
|-|-|-|
|URL|`https://stat.ink/api/v2/battle`|`https://stat.ink/api/v2/user-battle`|
|Return-Type|`application/json`|`application/json`|
|認証|なし|[必要](authorization.md)|


バトル情報の一覧をJSON形式、[`battle`構造体](struct/battle.md)の配列で返します。

`only=splatnet_number` が指定されているときは、数値の配列で返します。

このAPIは結構重いのでほどほどの頻度で叩いてください。



クエリパラメータ
----------------

|パラメータ名|型|内容|
|------------|--|----|
|`screen_name`|文字列|指定されたユーザのデータのみを検索します。指定しなければすべてのユーザを検索します。<br>`user-battle`ではこのパラメータは指定できません。|
|`newer_than`|数値|指定されたバトルIDよりも新しいバトルを検索します。指定した数値そのものは含みません。|
|`older_than`|数値|指定されたバトルIDよりも古いバトルを検索します。指定した数値そのものは含みません。|
|`count`|数値(1～50)|指定した個数を上限に検索します。|
|`order`|指定文字列|`asc` : 投稿が古い順に表示します。<br>`desc` : 投稿が新しい順に表示します。<br>`splatnet_asc` : イカリング2の識別番号が小さい順に表示します。<br>`splatnet_desc` : イカリング2の識別番号が大きい順に表示します。<br>デフォルトは`desc`です。ただし、`only`によって動作が変わります。|
|`only`|指定文字列|`splatnet_number` : イカリング2の識別番号のみを返します。|


`user-battle` について
----------------------

`user-battle`を使用すると、`Authorization`ヘッダで認証されたユーザについての情報を返します。

これによって、「自分の情報を取得したいが、APIキーはわかっても、`screen_name`はわからない」ときに対応できます。


`only=splatnet_number` について
-------------------------------

`only=splatnet_number` を指定すると、次のような状態になります。

  - イカリング2のバトル番号のみの配列が返ります。
  - イカリング2のバトル番号が指定されていないバトルは無視されます。
  - `order` のデフォルトが `splatnet_desc` に変わります。（上書き可能）


----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
