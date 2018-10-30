`GET /api/v2/salmon`, `GET /api/v2/user-salmon`
===============================================

UNDER CONSTRUCTION. THIS API DOES NOT WORK YET.

| |`/api/v2/battle`|`/api/v2/user-battle`|
|-|-|-|
|URL|`https://stat.ink/api/v2/salmon`|`https://stat.ink/api/v2/user-salmon`|
|Return-Type|`application/json`|`application/json`|
|Auth|No|[Needed](authorization.md)|

Get Salmon Run work results as JSON data.


Query Parameter
---------------

All parameters are optional.

|Parameter|Type| |
|---------|----|-|
|`screen_name`|string|Filter by user. If omitted, all data will return.<br>You can't specify on `user-salmon` API.|
|`only`|key-string|`splatnet_number`: Returns only SplatNet's ID Numbers.|
|`stage`|key-string|e.g. `dam`. Filter by stage.|
|`newer_than`|integer|Filter by stat.ink's ID. You will get `newer_than` < `id` < `older_than`.|
|`older_than`|integer|See `newer_than`|
|`order`|key-string|`asc`: older to newer<br>`desc`: newer to older<br>`splanet_asc`: SplatNet number small to big<br>`splatnet_desc`: SplatNet number big to small<br>Default (if `only` = `splatnet_number): `splatnet_desc`<br>Default (otherwise): `desc`|
|`count`|integer|Max count to get.<br>Accepts (if `only` = `splatnet_number`): 1-1000<br>Accepts (otherwise): 1-50<br>Default: 50|

Query example: `https://stat.ink/api/v2/salmon?screen_name=fetus_hina&stage=dam`

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
