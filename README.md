stat.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/stat.ink.svg)](https://github.com/fetus-hina/stat.ink/blob/master/LICENSE)
[![Actions Status](https://github.com/fetus-hina/stat.ink/workflows/CI/badge.svg)](https://github.com/fetus-hina/stat.ink/actions)
[![Greenkeeper badge](https://badges.greenkeeper.io/fetus-hina/stat.ink.svg)](https://greenkeeper.io/)

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

- PHP 7.4+
  - Doesn't work with 7.3 or lower. (Uses statements and constants added in v7.4, such as arrow function and typed property)
  - You should build/install with Argon2. [Install `php-sodium` if you use remirepo's PHP 7.4](https://github.com/remicollet/remirepo/issues/132#issuecomment-566513636).
- PostgreSQL 9.5+ (Recommended: 11+)
  - Doesn't work with 9.4 or lower. (Uses features added in v9.5) 
- ImageMagick (`convert`)
- Node.js (`npm`)
  - Recommended: latest release or latest LTS
- `jpegoptim`
- Brotli (`brotli` or `bro`)
- MaxMind's account

https://stat.ink/ works with:

- CentOS 7.8 (x86-64)
- EPEL
  - `brotli`
- [JP3CKI Repository](https://rpm.fetus.jp/)
  - [H2O](https://h2o.examp1e.net/) mainline
- [Remi's RPM repository](http://rpms.famillecollet.com/)
  - `remi-safe` repository, it uses SCL mechanism
      - PHP 7.4.*
          - `php74-php-cli`
          - `php74-php-fpm`
          - `php74-php-gd`
          - `php74-php-intl`
          - `php74-php-mbstring`
          - `php74-php-mcrypt`
          - `php74-php-pdo`
          - `php74-php-pecl-msgpack`
          - `php74-php-pgsql`
* [Node.js Repository](https://nodejs.org/en/download/package-manager/#enterprise-linux-and-fedora)
    - [Node.js](https://nodejs.org/)
        - `nodejs`
* [PostgreSQL Official Repository](https://www.postgresql.org/download/linux/redhat/)
    - PostgreSQL 11.x
      - `postgresql11`
      - `postgresql11-server`

Notes:

  - Default version of PHP on CentOS 7 is 5.4.16. This application doesn't work on it.  
    We are using features and statements that were added up to PHP 7.4.

  - Default version of PostgreSQL on CentOS 7 is 9.2.14. This application doesn't work with it.  
    We are using features added in PostgreSQL 9.5 (e.g., jsonb, UPSERT).  
    We use PostgreSQL 11 in our actual system, but 9.5 will work just fine.


### MaxMind's Account

Stat.ink uses the GeoIP database for purposes such as detecting the user's time zone.  
You need to register an account on MaxMind to download the database (No additional cost required).

  1. [Sign up for MaxMind account](https://www.maxmind.com/en/geolite2/signup) (or just log in)
  2. Access to "My License Key" page and click "Generate new license key."
  3. Fill in "License key description" and issue a license key.
  4. Note the license key. The license key won't be displayed again.

After issuing the license key, set the license key to the environment variable "`GEOIP_LICENSE_KEY`".  
If you are using bash, you may want to add the following to your `~/.bashrc`:

```sh
export GEOIP_LICENSE_KEY=ABCDEFGHIJKLMNOP
```

After editing `.bashrc`, reopen the shell or remember `source ~/.bashrc`.


Branches
--------

We have 2 main branches. The one is `master` and the other is `dev`.

### `master` branch ###

This branch is deployed to the server.
Changes are merged from the `dev` branch at irregular intervals.

When you contribute to us, you should not request changes to this branch.


### `dev` branch ###

The development of the app takes place on this branch.

If you think you're going to make a pull request, make the change from this branch.



使い方 HOW TO USE (DEVELOPER)
-----------------------------

### SETUP ###

Recommend: [Setup with Vagrant](https://github.com/statink/devenv-vagrant)

Another way: [How to setup a development environment](https://github.com/fetus-hina/stat.ink/wiki/How-to-setup-a-development-environment)

Note: Docker way is abandoned.

### UPDATE ###

```sh
git fetch --all && \
  git merge --ff-only origin/master && \
  ./composer.phar install --prefer-dist && \
  make && \
  rm -rfv web/assets/*
```

assets の中身は消さなくても動くことがありますが、動かないこともあるので消す事をおすすめします。

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

### Needs test site?

You can use staging environment for POST API test.  
URL: `https://test.stat.ink/` instead of `https://stat.ink/`.

The database of statging environment will reset daily.  
The maintenance process will be started at 23:00 UTC and will take 1.5 hours.


ライセンス LICENSE
-----------------

```
The MIT License (MIT)

Copyright (c) 2015-2020 AIZAWA Hina <hina@fetus.jp>

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
