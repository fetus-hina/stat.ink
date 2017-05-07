Authorization
=============

stat.inkではAPIへのアクセス認可が必要な場合、次の方法を利用してのアクセスが必要となります。

  - ユーザごとに割り当てられたAPIキーを用います。（このAPIキーはv1 APIと共通です）

  - `Authorization: Bearer` HTTPヘッダを利用します。


APIキーの取得
-------------

stat.ink webアプリケーションは、ウェブサイト上から会員登録を行うと、各ユーザにAPIキーを発行します。

このAPIキーは各ユーザのプロフィール設定画面から表示できます。

各アプリケーションはこのAPIキーをユーザに入力してもらうことによって連携を行います。

なお、APIキーは`/^[0-9A-Za-z_-]{43}$/`です。
（Base64の `+` `/` が `-` `_` に置き換えられ、`=` がない 43 文字と等しい）


リクエストの送信
----------------

認可が必要なAPIエンドポイント（例えばバトル投稿）にアクセスする際、次のように Bearer スキームを利用した
Authorization ヘッダを付加して要求を投げる必要があります。

例: (API key = `sD093VHLHW41b9xdaM7zVpyIX2TbIornR0h47RaUNGA`)
```
POST /api/v2/endpoint HTTP/1.1
Host: stat.ink
Authorization: Bearer sD093VHLHW41b9xdaM7zVpyIX2TbIornR0h47RaUNGA
Content-Type: application/json
Content-Length: 42

...
```
