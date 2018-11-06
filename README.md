stat.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/stat.ink.svg)](https://github.com/fetus-hina/stat.ink/blob/master/LICENSE)
[![StyleCI](https://styleci.io/repos/42917467/shield?branch=master)](https://styleci.io/repos/42917467)

Source codes for https://stat.ink/

[IkaLog](https://github.com/hasegaw/IkaLog), SquidTracks, splatnet2statink 等の対応ソフトウェア、または自作のソフトウェアと連携することで Splatoon の戦績を保存し、統計を取ります。

バグレポート BUG REPORT
----------------------

- [GitHub で問題を報告する(要GitHubアカウント) Submit an issue on GitHub (Need an account)](https://github.com/fetus-hina/stat.ink/issues)
- Contact to administrator with email or twitter.

バグレポートは日本語で大丈夫です。開発者は日本語しかまともに使えない日本人です。

I'll accept your bug report in English or Japanese.   
The administrator is not goot at English. Please use easy English.

問題がセキュリティにかかわるものであれば、非公開の方法を利用してください。  
Use a private channel if it is a security issue.

- Use Direct Message of twitter. ツイッターのDMを使う
- [Use an encrypted message with PGP(GPG). PGP(GPG)で暗号化して送信する](https://fetus.jp/about/pgp)


REQUIREMENTS
------------

* PHP 7.1+
  - Doesn't work with 7.0 or lower. (Uses statements added in v7.1)
* PostgreSQL 9.5+ (Recommended: 10+)
  - Doesn't work with 9.4 or lower. (Uses features added in v9.5) 
* Gearman
* ImageMagick (`convert`)
* Node.js (`npm`)
  - Recommended: 6.x or 7.x
* `jpegoptim`
* `pngcrush`
* Brotli (`bro`)

https://stat.ink/ works with:

- CentOS 7.4.1708 (x86-64)
- EPEL
  - `gearmand`
- [JP3CKI Repository](https://rpm.fetus.jp/)
  - [H2O](https://h2o.examp1e.net/) mainline
  - [Brotli](https://github.com/google/brotli)
- [Remi's RPM repository](http://rpms.famillecollet.com/)
  - `remi-safe` repository, it uses SCL mechanism
      - PHP 7.2.*
          - `php72-php-cli`
          - `php72-php-fpm`
          - `php72-php-gd`
          - `php72-php-intl`
          - `php72-php-mbstring`
          - `php72-php-mcrypt`
          - `php72-php-pdo`
          - `php72-php-pecl-gearman`
          - `php72-php-pecl-msgpack`
          - `php72-php-pgsql`
* [Node.js Repository](https://nodejs.org/en/download/package-manager/#enterprise-linux-and-fedora)
    - [Node.js](https://nodejs.org/)
        - `nodejs`
* [PostgreSQL Official Repository](https://www.postgresql.org/download/linux/redhat/)
    - PostgreSQL 10.x
      - `postgresql10`
      - `postgresql10-server`

※CentOS 7 の標準 PHP は 5.4.16 です。このバージョンでは動作しません。<br>
　PHP 7.1 で追加された機能を使用しています（`TheClass::class`、匿名クラス、戻り型のnullableつきのヒントなど）

※CentOS 7 の標準 PostgreSQL のバージョンは 9.2.14 です。このバージョンでは動作しません。<br>
　PgSQL 9.5 で追加された機能を使用しています（jsonb 型、UPSERT など）<br>
　実際のサーバでは PgSQL 10 を使用していますが、現時点では 9.5 で充分動作するはずです。<br>
　ただし、将来必要が生じた場合はためらわずに PgSQL 10 (以降) に依存させます。

使い方 HOW TO USE (DEVELOPER)
-----------------------------

### SETUP ###

[開発環境の作り方](https://github.com/fetus-hina/stat.ink/wiki/%E9%96%8B%E7%99%BA%E7%92%B0%E5%A2%83%E3%81%AE%E3%82%BB%E3%83%83%E3%83%88%E3%82%A2%E3%83%83%E3%83%97) /
[How to setup a development environment](https://github.com/fetus-hina/stat.ink/wiki/How-to-setup-a-development-environment)

Dockerfile を見ても構築の手順が記載されています（Dockerfile は自動化と docker の仕組みの都合上、かなり無理矢理やっている箇所があります）


### UPDATE ###

こういうことをやればよさそうな気がします。何をやっているか確認したあと実行してください。

```sh
git fetch --all && \
  git merge --ff-only origin/master && \
  ./composer.phar install && \
  make && \
  rm -rfv web/assets/* runtime/Smarty/compile/*
```

assets の中身や compile の中身は消さなくても動くことがありますが、動かないこともあるので消す事をおすすめします。

なお、assets ディレクトリ自体を消してしまった場合は実行エラーが発生しますので中身だけ消してください。


### DOCKER ###

**DOCKER IMAGE IS ABANDONED AND NO LONGER MAINTAINED.**

テスト環境構築用の `Dockerfile` が同梱されています。あるいは、Docker Hub の [`jp3cki/statink`](https://hub.docker.com/r/jp3cki/statink/) でビルド済みのイメージが取得できます。

主要なソフトウェアのバージョンが合わせてあるため、本番環境とほぼ同じ環境ができあがるはずです。

データの永続化に関する配慮は一切ありません。つまり、コンテナを起動する度にユーザやバトルは全部消えます。

自分でイメージを作成する場合、現在の作業ディレクトリの中身が `/home/statink/stat.ink` にデプロイされます。その際 `vendor` などは一度消され、再構成されます。

コンテナを起動すると 80/TCP で H2O が待ち受けています。ここへ接続して使用します。必要であれば `docker run` する時に `-p 8080:80` のように任意のポートにマップしてください。


※Docker の本来のポリシーに反して、1アプリケーション1コンテナの形式になっています（内部で複数のdaemonが動作します）。

※永続化のためのヒント:

  - /home/statink/stat.ink/config
  - /home/statink/stat.ink/web/images
  - /var/opt/rh/rh-postgresql95/lib/pgsql/data

自分で永続化したことがないのでうまく行くかは知りません。


API
---

stat.ink にデータを投稿する、または取得する API は次のページを参照してください。

- [API for Splatoon 2](https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/)
- [API for Splatoon 1](https://github.com/fetus-hina/stat.ink/blob/master/API.md)


ライセンス LICENSE
-----------------

```
The MIT License (MIT)

Copyright (c) 2015-2017 AIZAWA Hina <hina@bouhime.com>

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

ライセンス（ドキュメント類） LICENSE (DOCUMENTS)
----------------------------------------------

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

ドキュメント類のライセンスは[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。

Documents of stat.ink project are licensed under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/deed.en).
