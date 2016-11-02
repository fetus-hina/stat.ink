`PATCH /api/v1/battle` (version: v1.94 or later)
======================

URL: `https://stat.ink/api/v1/battle`

Method: `PATCH`

Return-Type: `application/json`

※URLが一般的なRESTと異なりますので注意してください。（リソースIDを含むURIにPATCHを送るのではありません）

リクエスト方法
--------------

`PATCH` メソッドを使用することが可能な場合は `PATCH /api/v1/battle HTTP/1.1` のように `PATCH` メソッドを使用してください。

`PATCH` メソッドを使用することができない場合は、 `POST` を使用します。
この場合は、`_method` パラメータに `PATCH` を与えることで本来は `PATCH` メソッドであることを伝えてください。


### リクエスト例 ###

```
PATCH /api/v1/battle HTTP/1.1
Host: stat.ink
Content-Type: application/json
Content-Length: ***

{"apikey":"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa","id":42}
```

```
POST /api/v1/battle HTTP/1.1
Host: stat.ink
Content-Type: application/json
Content-Length: ***

{"_method":"PATCH","apikey":"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa","id":42}
```


パラメータ
----------

### 基本パラメータ ###

* `apikey` : (必須) ユーザを特定するための[APIキー](structure/apikey.md)を指定します。

* `id` : (必須) 編集対象のバトルのIDを指定します。

* `test` : 通常は送信しません。API テスト時に指定すると実際の反映を行わずに動作試験が行えます。
    - `validate` : 送信内容のバリデーションのみを行います。エラーもしくは簡素なレスポンス `{"validate": true}` が返ります。
    - `dry_run` : 通常の成功時と同じレスポンスが返ります。

### 修正対象に関するパラメータ ###

次のパラメータのうち 1 つ以上指定されることを期待しますが、指定しないからといって特にエラーにはなりません（何も起きません）。

* `link_url` : バトルに関連付けるURLを指定します。`http://` または `https://` から始まる完全なURLを指定する必要があります。システムとしてはここにはYoutubeの動画URLを指定することを想定しています。
    - 空文字列やnull、未指定: 現在の値を維持します。（何もしません）
    - `<<DELETE>>` : この特殊な値を設定すると、現在設定されている値が消去されます。
    - `http://example.com/...` (完全なURL) : このURLに変更します。

* `note` : メモ（公開用）を指定します。長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。
    - 空文字列やnull、未指定 : 現在の値を維持します。（何もしません）
    - `<<DELETE>>` : この特殊な値を設定すると、現在設定されている値が消去されます。
    - その他任意の文字列: この文字列に更新されます。

* `private_note` : メモ（公開用）を指定します。長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。
    - 空文字列やnull、未指定 : 現在の値を維持します。（何もしません）
    - `<<DELETE>>` : この特殊な値を設定すると、現在設定されている値が消去されます。
    - その他任意の文字列: この文字列に更新されます。

その他の属性に対するPATCHの実装は未定です。


パラメータ サンプル
-------------------

JSON「のような形式」で記載します。

```js
{
    "apikey":       "APIKEYHERE",
    "id":           42, // battle id = 42 を更新対象とします
    "link_url":     "https://example.com/foo", // このURLに更新します
    "note":         "", // 現在設定されているメモのまま維持します
    "private_note": "<<DELETE>>", // 現在設定されている非公開メモを削除します

    // "_method": "POST",
    // "test": "validate",
}
```

応答
----

### リクエストが壊れているなどアプリケーションが正常に実行されなかった場合 ###

HTTPステータスコードと通常のHTMLで応答が返ります。


### パラメータ異常などvalidateに失敗した場合 ###

[エラー構造体](structure/error.md)でエラーが返ります。

### validateに成功し、`test`=`validate`の場合 ###

`{"validate":true}` と等価なJSONが返ります。

### 更新操作を行った場合、または更新するべき内容がなかった場合 ###

`POST /api/v1/battle` あるいは `GET /api/v1/battle` と同様のレスポンスが返ります。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
