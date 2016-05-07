`DELETE /api/v1/battle`
====================

URL: `https://stat.ink/api/v1/battle`

Method: `DELETE`

Return-Type: `application/json`


リクエスト方法
--------------

`DELETE` メソッドを使用することが可能な場合は `DELETE /api/v1/battle HTTP/1.1` のように `DELETE` メソッドを使用してください。

`DELETE` メソッドを使用することができない場合は、 `POST` を使用します。
この場合は、`_method` パラメータに `DELETE` を与えることで本来は `DELETE` メソッドであることを伝えてください。

### リクエスト例 ###

```
DELETE /api/v1/battle HTTP/1.1
Host: stat.ink
Content-Type: application/json
Content-Length: 64

{"apikey":"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa","id":42}
```

```
POST /api/v1/battle HTTP/1.1
Host: stat.ink
Content-Type: application/json
Content-Length: 83

{"_method":"DELETE","apikey":"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa","id":42}
```


パラメータ
----------

* `apikey` : (必須) ユーザを特定するための[APIキー](structure/apikey.md)を指定します。

* `id` : (必須) 削除対象のバトルのIDをスカラ値か配列で指定します。IDは100個まで指定できます。
    - `"id":42` : ID=42 のバトルを削除します。
    - `"id":[42,43] : ID=42, ID=43 のバトルをそれぞれ削除します。

* `test` : 通常は送信しません。API テスト時に指定すると実際の反映を行わずに動作試験が行えます。
    - `validate` : 送信内容のバリデーションのみを行います。エラーもしくは簡素なレスポンス `{"validate": true}` が返ります。
    - `dry_run` : 通常の成功時と同じレスポンスが返ります。


応答
----

### リクエストが壊れているなどアプリケーションが正常に実行されなかった場合 ###

HTTPステータスコードと通常のHTMLで応答が返ります。


### パラメータ異常などvalidateに失敗した場合 ###

[エラー構造体](structure/error.md)でエラーが返ります。

### validateに成功し、`test`=`validate`の場合 ###

`{"validate":true}` と等価なJSONが返ります。

### 削除操作を行った場合 ###

次のようなレスポンスが返ります。
HTTPレスポンスは実際の操作が仮に成功していなくても `200 OK` になります。

```js
{
    "deleted": [
        {
            "id": 42,
            "error": null
        }
    ],
    "not-deleted": [
        {
            "id": 100,
            "error": "not found"
        },
        {
            "id": 101,
            "error": "automated result"
        }
    ]
}
```

この場合は、ID=42 の削除に成功し、100, 101 の削除に失敗しています。

指定された ID がひとつも削除できなかった場合は `deleted` が空の配列に、
指定された ID がすべて削除できた場合は `not-deleted` が空の配列になります。

`error` が示す内容は次の通りです。

* `deleted`:
    - 必ず `null` になります。

* `not-deleted`:
    - `"not found"` : 指定された ID のバトルが存在しない場合に設定されます。
    - `"user not match"` : APIキーで与えられたユーザと異なるユーザのバトルが指定された場合に設定されます。
    - `"automated result"` : 「自動化されたバトル結果」と認識している場合に設定されます。現在自動化されているとしているバトルは削除できません。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
