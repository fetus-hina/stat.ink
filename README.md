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

* PHP 5.5+
* PostgreSQL 9.4+
* ImageMagick (`convert`)
* Node.js (`npm`)
* `cwebp`
* `jpegoptim`
* `pngcrush`

https://stat.ink/ は現在次の構成で動作しています。（Docker で用意しているものとほぼ同じです）

* CentOS 7.1.1503 (x86_64)
* Nginx 1.9.x (mainline)
* [SCL](https://www.softwarecollections.org/)
    - [rh-php56](https://www.softwarecollections.org/en/scls/rhscl/rh-php56/)
        - PHP 5.6.*
            - `rh-php56-php-cli`
            - `rh-php56-php-gd`
            - `rh-php56-php-intl`
            - `rh-php56-php-mbstring`
            - `rh-php56-php-pdo`
        - PHP-FPM
            - `rh-php56-php-fpm`
    - [php56more](https://www.softwarecollections.org/en/scls/remi/php56more/)
        - Mcrypt
            - `more-php56-php-mcrypt`
            - `more-php56-php-pecl-msgpack`
    - [rh-postgresql94](https://www.softwarecollections.org/en/scls/rhscl/rh-postgresql94/)
        - PostgreSQL 9.4.*
            - `rh-postgresql94-postgresql`
            - `rh-postgresql94-postgresql-server`
    - [v8314](https://www.softwarecollections.org/en/scls/rhscl/v8314/)
        - V8 3.14.* (Used by Node.js)
    - [nodejs010](https://www.softwarecollections.org/en/scls/rhscl/nodejs010/)
        - Node.js 0.10.*
            - `nodejs010-nodejs`
            - `nodejs010-npm`


CentOS 7 の標準 PHP は 5.4.16 です。このバージョンでは動作しません。

CentOS 7 の標準 PostgreSQL のバージョンは 9.2.14 です。このバージョンでは動作しません。


使い方
------

### SETUP ###

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

コンテナを起動すると 80/TCP で Nginx が待ち受けています。ここへ接続して使用します。必要であれば `docker run` する時に `-p 8080:80` のように任意のポートにマップしてください。


API
---

stat.ink にデータを投稿する、または取得する API は次のページを参照してください。
[API.md](https://github.com/fetus-hina/stat.ink/blob/master/API.md)


ライセンス
----------

The MIT License (MIT)

Copyright (c) 2015 AIZAWA Hina \<hina@bouhime.com\>

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
