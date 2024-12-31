stat.ink
========

[![status](https://img.shields.io/badge/status-Kener-blue?style=flat)](https://status.stat.ink)

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/stat.ink.svg)](https://github.com/fetus-hina/stat.ink/blob/master/LICENSE)
[![Actions Status](https://github.com/fetus-hina/stat.ink/workflows/CI/badge.svg)](https://github.com/fetus-hina/stat.ink/actions)

[![](https://discordapp.com/api/guilds/668761479691894785/widget.png?style=banner2)](https://discord.gg/DyWTsKRNvT)

Source codes for https://stat.ink/

SquidTracks, splatnet2statink, IkaLog, s3s 等の対応ソフトウェア、または自作のソフトウェアと連携することで Splatoon 1, 2, 3 の戦績を保存し、統計を取ります。

This software will save your Splatoon 1, 2, 3 battle results and get statistics by integrating with "supported software" such as
SquidTracks, splatnet2statink, IkaLog, s3s, etc., or your own app.


バグレポート BUG REPORT
----------------------

- (推奨) [GitHub で問題を報告する(要GitHubアカウント)](https://github.com/fetus-hina/stat.ink/issues)  
  (Recommend) [Submit an issue on GitHub (Need an account)](https://github.com/fetus-hina/stat.ink/issues)
- メールかTwitterで連絡する  
  Contact to administrator with email or twitter.

バグレポートは日本語で大丈夫です。開発者は日本語しかまともに使えない日本人です。

I'll accept your bug report in English or Japanese.   
The administrator is not goot at English. Please use easy English and do not use idioms or slangs.

問題がセキュリティにかかわるものであれば、非公開の方法を利用してください。  
Use a private channel if it is a security issue.

- ツイッターのDMを使う  
  Use Direct Message of twitter.
- [PGP(GPG)で暗号化して送信する](https://fetus.jp/about/pgp)  
  [Use an encrypted message with PGP(GPG)](https://fetus.jp/about/pgp)
  - GitHubのissueに、暗号化したメッセージを貼り付けることができます。  
    You can paste an encrypted message to our "issue."


REQUIREMENTS
------------

- PHP 8.2 or PHP 8.3
  - PHP 8.1以下では動作しません。（8.2で追加された構文等を利用しています）  
    Doesn't work with 8.1 or lower. (Uses statements and constants added in v8.2)
  - Argon2が有効化されたPHPが必要です。RemirepoのPHPを利用している場合、`php-sodium`をインストールしてください。
    You should build/install with Argon2. Install `php-sodium` if you use remirepo's PHP
- PostgreSQL 11
  - PgSQL 10以下では動作しません（11で追加された機能を利用しています）  
    Doesn't work with 10 or lower. (Uses features added in v11)
- ImageMagick (`convert`)
- Node.js (`npm`)
  - Recommended: latest release or latest LTS
- `jpegoptim`
- MaxMind's account

https://stat.ink/ works with:

- RockyLinux 9 (x86-64)
- EPEL
- [JP3CKI Repository](https://rpm.fetus.jp/)
  - [H2O](https://h2o.examp1e.net/) mainline
- [Remi's RPM repository](http://rpms.famillecollet.com/)
  - `remi-modular` repository, with `dnf enable php:remi-8.3`
      - PHP 8.3
          - `composer`
          - `php-cli`
          - `php-fpm`
          - `php-gd`
          - `php-intl`
          - `php-mbstring`
          - `php-pdo`
          - `php-pecl-msgpack`
          - `php-pgsql`
          - `php-sodium`
- Node.js with `dnf enable nodejs:20`
    - [Node.js](https://nodejs.org/)
        - `nodejs`
        - `npm`
- [PostgreSQL Official Repository](https://www.postgresql.org/download/linux/redhat/)
    - PostgreSQL 11.x
      - `postgresql11`
      - `postgresql11-server`

### MaxMind's Account

stat.inkは利用者のタイムゾーンの検出等にGeoIPデータベースを利用します。
データベースをダウンロードするには、MaxMindへの会員登録が必要です。（無料）

Stat.ink uses the GeoIP database for purposes such as detecting the user's time zone.
You need to register an account on MaxMind to download the database (No additional cost required).

  1. [MaxMindに会員登録します](https://www.maxmind.com/en/geolite2/signup)（またはログインします）  
     [Sign up for MaxMind account](https://www.maxmind.com/en/geolite2/signup) (or just log in)

  2. 「My License Key」ページにアクセスし、「Generate new license key」をクリックします。  
     Access to "My License Key" page and click "Generate new license key."

  3. 「License key description」を埋め、ライセンスキーを発行します。  
     Fill in "License key description" and issue a license key.

  4. ライセンスキーを記録します。ライセンスキーはこれを最後に表示されないので注意してください。  
     Note the license key. The license key won't be displayed again.

ライセンスキーを発行したら、環境変数 `GEOIP_LICENSE_KEY` に設定します。  
bashを利用しているなら、`~/.bashrc` に追加するなどの方法があります。

After issuing the license key, set the license key to the environment variable "`GEOIP_LICENSE_KEY`".  
If you are using bash, you may want to add the following to your `~/.bashrc`:

```sh
export GEOIP_LICENSE_KEY=ABCDEFGHIJKLMNOP
```

`.bashrc` を編集したら、シェルを再起動するか、`source ~/.bashrc` してください。

After editing `.bashrc`, reopen the shell or remember `source ~/.bashrc`.


Branches
--------

2つの主たるブランチがあります。 `master` と `dev` です。

We have 2 main branches. The one is `master` and the other is `dev`.

### `master` branch ###

このブランチがサーバにデプロイされます。
`dev` ブランチから不定期に変更が取り込まれます。

This branch is deployed to the server.
Changes are merged from the `dev` branch at irregular intervals.

コントリビュートいただく場合、このブランチへプルリクエストは行わないでください。

When you contribute to us, you should not request changes to this branch.

`master` はただの識別子です。政治的・差別的意図はありません。

The word `master` is just an identifier. There are no political or discriminatory intentions.


### `dev` branch ###

アプリの開発はこのブランチで行います。

The development of the app takes place on this branch.

プルリクエストを作成する場合、このブランチから/へ行ってください。

If you think you're going to make a pull request, make the change from this branch.



使い方 HOW TO USE (DEVELOPER)
-----------------------------

**We are switching from CentOS 7 to RockyLinux 9 and cannot set up the system in the way shown here.**

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
  ./yii asset/up-revision
```

API
---

stat.ink にデータを投稿する、または取得する API は次のページを参照してください。 
See the pages below for APIs to post and retrieve data from stat.ink.

- API for Splatoon 3: [See Wiki on GitHub](https://github.com/fetus-hina/stat.ink/wiki)
- [API for Splatoon 2](https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/)
- [API for Splatoon 1](https://github.com/fetus-hina/stat.ink/blob/master/API.md)


コーディング規約 CODING STANDARDS
---------------------------------

  | Language    | Coding Standards |
  |-------------|------------------|
  | PHP         | [PSR-12](https://www.php-fig.org/psr/psr-12/)-based, See phpcs.xml |
  | PHP (views) | Indent with 2 spaces |
  | JavaScript  | [semistandard](https://github.com/standard/semistandard#rules) |
  | SCSS / CSS  | [Sass Guidelines](https://sass-guidelin.es/)-based, See .stylelintrc


ライセンス LICENSE
------------------

```
The MIT License (MIT)

Copyright (c) 2015-2024 AIZAWA Hina <hina@fetus.jp>

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

ライセンス（アプリケーションテンプレート） LICENSE (App Template)
-----------------------------------------------------------------

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
------------------------------------------------

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

ドキュメント類のライセンスは[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。

Documents of stat.ink project are licensed under a [Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/deed.en).
