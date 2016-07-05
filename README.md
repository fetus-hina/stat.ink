stat.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/stat.ink.svg)](https://github.com/fetus-hina/stat.ink/blob/master/LICENSE)
[![Dependency Status](https://www.versioneye.com/user/projects/56167010a19334001e000337/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56167010a19334001e000337)
[![Dependency Status](https://www.versioneye.com/user/projects/5616700aa1933400190005db/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5616700aa1933400190005db)

https://stat.ink/ のソースコードです。

[IkaLog](https://github.com/hasegaw/IkaLog) 等の対応ソフトウェア、または自作のソフトウェアと連携することで Splatoon の戦績を保存し、統計を取ります。


動作環境
--------

* PHP 7.0+ (5.6 以下では動作しません)
* PostgreSQL 9.5+ (9.4 以下では動作しません)
* ImageMagick (`convert`)
* Node.js (`npm`)
* `jpegoptim`
* `pngcrush`
* Brotli (`bro`)

https://stat.ink/ は現在次の構成で動作しています。（Docker で用意しているものとほぼ同じです）

* CentOS 7.2.1511 (x86_64)
* [JP3CKI Repository](https://rpm.fetus.jp/)
    - [H2O](https://h2o.examp1e.net/) 2.1
    - [Brotli](https://github.com/google/brotli)
* [Software Collections](https://www.softwarecollections.org/)
    - [rh-postgresql95](https://www.softwarecollections.org/en/scls/rhscl/rh-postgresql95/)
        - PostgreSQL 9.5.*
            - `rh-postgresql95-postgresql`
            - `rh-postgresql95-postgresql-server`
    - [rh-nodejs4](https://www.softwarecollections.org/en/scls/rhscl/rh-nodejs4/)
        - Node.js 4.*
            - `rh-nodejs4-nodejs`
            - `rh-nodejs4-npm`
* [Remi's RPM repository](http://rpms.famillecollet.com/)
    - `remi-safe` repository, it uses SCL mechanism
        - PHP 7.0.*
            - `php70-php-cli`
            - `php70-php-fpm`
            - `php70-php-gd`
            - `php70-php-intl`
            - `php70-php-mbstring`
            - `php70-php-mcrypt`
            - `php70-php-pdo`
            - `php70-php-pecl-msgpack`
            - `php70-php-pgsql`

CentOS 7 の標準 PHP は 5.4.16 です。このバージョンでは動作しません。（PHP 7.0 で追加された機能を使用しています（`TheClass::class`、匿名クラスなど））

CentOS 7 の標準 PostgreSQL のバージョンは 9.2.14 です。このバージョンでは動作しません。（PgSQL 9.5 で追加された機能を使用しています（jsonb 型、UPSERT など））

使い方
------

### SETUP ###

（簡略化して記載しています。気合で頑張るか、Dockerfile を見てください。ただし Dockerfile は自動化のために割と無理矢理な方法をとっている箇所があります）

1. `git clone` します

    ```sh
    git clone https://github.com/fetus-hina/stat.ink.git stat.ink
    cd stat.ink
    ```

2. `make init` します。

    ```sh
    make init
    ```

3. `config/db.php` が作成されています。 `config/db.php` をお好きな設定に変更するかそのままにするかは自由ですが、その設定で繋がるようにデータベースを設定します。

    ```sh
    su - postgres
    # pg_hba.conf 等を適切に設定します。必要であれば PostgreSQL サーバを再起動します。
    createuser -DEPRS statink
    # パスワードの入力を求められます。
    # config/db.php の自動生成パスワードを入力するか、
    # パスワードを任意に決めた上で config/db.php を書き換えてください。
    createdb --encoding=UTF-8 --owner=statink --template=template0 statink
    exit
    ```

4. `make` します。

    ```sh
    make
    ```

5. ウェブサーバとかを良い感じにセットアップするときっと動きます。


### UPDATE ###

こういうことをやればよさそうな気がします。何をやっているか確認したあと実行してください。

```sh
git fetch --all && \
  git merge --ff-only origin/master && \
  ./composer.phar install && \
  make && \
  rm -rfv web/assets/* runtime/Smarty/compile/*
```


### DOCKER ###

テスト環境構築用の `Dockerfile` が同梱されています。あるいは、Docker Hub の [`jp3cki/statink`](https://hub.docker.com/r/jp3cki/statink/) でビルド済みのイメージが取得できます。

主要なソフトウェアのバージョンが合わせてあるため、本番環境とほぼ同じ環境ができあがるはずです。

データの永続化に関する配慮は一切ありません。つまり、コンテナを起動する度にユーザやバトルは全部消えます。

現在の作業ディレクトリの中身が `/home/statink/stat.ink` にデプロイされます。その際 `vendor` などは一度消され、再構成されます。

コンテナを起動すると 80/TCP で H2O が待ち受けています。ここへ接続して使用します。必要であれば `docker run` する時に `-p 8080:80` のように任意のポートにマップしてください。


API
---

stat.ink にデータを投稿する、または取得する API は次のページを参照してください。
[API.md](https://github.com/fetus-hina/stat.ink/blob/master/API.md)


ライセンス
----------

```
The MIT License (MIT)

Copyright (c) 2015-2016 AIZAWA Hina <hina@bouhime.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

ライセンス（アプリケーションテンプレート）
------------------------------------------

```
The Yii framework is free software. It is released under the terms of
the following BSD License.

Copyright © 2008 by Yii Software LLC (http://www.yiisoft.com)
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

 * Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in
   the documentation and/or other materials provided with the
   distribution.
 * Neither the name of Yii Software LLC nor the names of its
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
```

ライセンス（ドキュメント類）
----------------------------

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

ドキュメント類のライセンスは[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。

Documents of stat.ink project are licensed under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/deed.en).
