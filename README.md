stat.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/stat.ink.svg)](https://github.com/fetus-hina/stat.ink/blob/master/LICENSE)
[![Dependency Status](https://www.versioneye.com/user/projects/56167010a19334001e000337/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56167010a19334001e000337)
[![Dependency Status](https://www.versioneye.com/user/projects/5616700aa1933400190005db/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5616700aa1933400190005db)

https://stat.ink/ のソースコードです。

[IkaLog](https://github.com/hasegaw/IkaLog) 等の対応ソフトウェア、または自作のソフトウェアと連携することで Splatoon の戦績を保存し、統計を取ります（予定）。


動作環境
--------

* PHP 5.5+
* PostgreSQL 9.x
* Node.js (`npm`)
* pngcrush
* jpegoptim
* cwebp

https://stat.ink/ は現在次の構成で動作しています。

* CentOS 7.1.1503 (x86_64)
* Nginx 1.9.x (mainline)
* PostgreSQL 9.4.x (PGDG)
* Node.js 0.10.316 ([EPEL](https://fedoraproject.org/wiki/EPEL))
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

CentOS 7 の標準 PHP は 5.4.16 です。このバージョンでは動作しません。


使い方
------

### SETUP ###

1. `git clone` します

    ```sh
    git clone https://github.com/fetus-hina/stat.ink.git stat.ink
    cd stat.ink
    ```

2. `make` します。なお、初回はデータベースの準備ができていないため途中でエラー停止します。

    ```sh
    make
    ```

3. 初回の `make` で `config/db.php` が作成されています。 `config/db.php` をお好きな設定に変更するかそのままにするかは自由ですが、その設定で繋がるようにデータベースを設定します。

    ```sh
    su - postgres
    createuser -DEPRS statink
    # パスワードの入力を求められます。
    # config/db.php の自動生成パスワードを入力するか、
    # パスワードを任意に決めた上で config/db.php を書き換えてください。
    createdb --encoding=UTF-8 --owner=statink --template=template0 statink
    exit
    ```

4. もう一度 `make` します。今回は成功するはずです。

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
