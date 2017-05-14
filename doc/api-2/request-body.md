Request Body
============

stat.ink へ `POST` や `PUT`、`PATCH` で Content-Body を投げる方法です。

概要
----

次のいずれかの形式で表された Content Body を受け付けます。

|形式|`Content-Type`||
|-|-|-|
|JSON|`application/json`|バイナリデータを送信しないアプリに推奨|
|Message Pack|`application/x-msgpack`|バイナリデータを送信するアプリに推奨|
|URL Encoded|`application/x-www-form-urlencoded`|非推奨|
|Multipart Form-data|`multipart/form-data`|非推奨|

非推奨とされている形式について、この仕様書での送信方法の解説は行いません。


また、次の形式でエンコードされた Content Body を受け付けます。

|形式|`Content-Encoding`||
|-|-|-|
|(raw data)|なし|生データ|
|(raw data)|`identity`|生データ|
|Gzip|`gzip`|Gzip圧縮したデータ|


`Content-Type: application/json`
--------------------------------

- エンドポイントの仕様書で記載されている構造をそのまま JSON のオブジェクトに設定して送信します。

- パラメータが整数値の時、それが文字列として送信されても何ら問題はありません（`{"level": "10"}`は「妥当」）

- パラメータがブール値に見える時、必ず指定された文字列を送信してください。（`true` および `false` は受け付けません）

- バイナリデータ（画像など）を電文に含めることはできません。

例:

```
POST /api/v2/endpoint HTTP/1.1
Host: stat.ink
Authorization: Bearer APIKEYAPIKEYAPIKEYAPIKEY
Content-Type: application/json
Content-Encoding: identity
Content-Length: ***

{"key1":"value1","key2":42, ..}
```

`Content-Type: application/x-msgpack`
--------------------------------

- エンドポイントの仕様書で記載されている構造をそのまま MessagePack のオブジェクトに設定して送信します。

- パラメータが整数値の時、それが文字列として送信されても何ら問題はありません（`{"level": "10"}`は「妥当」）

- パラメータがブール値に見える時、必ず指定された文字列を送信してください。（`true` および `false` は受け付けません）

- バイナリデータ（画像など）を送信データに含めることができます（エンドポイントの仕様書で許可されている場合に限ります）

例:

```
POST /api/v2/endpoint HTTP/1.1
Host: stat.ink
Authorization: Bearer APIKEYAPIKEYAPIKEYAPIKEY
Content-Type: application/x-msgpack
Content-Encoding: identity
Content-Length: ***

(MessagePack binary)
```

`Content-Encoding: gzip`
------------------------

stat.ink への送信時には、必要に応じて JSON/MsgPack のデータを gzip で圧縮した後送信することができます。

イベントデータを含む場合など、データが大きい場合にそれなりの圧縮率が得られますが、
画像を含む場合などは思ったような圧縮率が得られませんから、全体としては期待した効果は得られない可能性が高いと思われます。

例:

```
POST /api/v2/endpoint HTTP/1.1
Host: stat.ink
Authorization: Bearer APIKEYAPIKEYAPIKEYAPIKEY
Content-Type: application/x-msgpack
Content-Encoding: gzip
Content-Length: [length(gzip(msgpack(data)]

[gzip(msgpack(data))]
```

V1との差異
---------

- 一般的なform送信時の形式が非推奨であること以外は大体同じです。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
