fest.ink
========

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![MIT License](https://img.shields.io/github/license/fetus-hina/fest.ink.svg)](https://github.com/fetus-hina/fest.ink/blob/master/LICENSE)
[![Dependency Status](https://www.versioneye.com/user/projects/55d469e7265ff60022000dc9/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d469e7265ff60022000dc9)
[![Dependency Status](https://www.versioneye.com/user/projects/55d469e9265ff6001a000e50/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55d469e9265ff6001a000e50)

https://fest.ink/ のソースコードです。

動作環境
--------

* PHP 5.5+
* SQLite3
* Node.js (`npm`)
* [webify](https://github.com/ananthakumaran/webify)

https://fest.ink/ は現在次の構成で動作しています。

* CentOS 7.1.1503 (x86_64)
* Nginx 1.9.x (mainline)
* SQLite 3.7.17 (標準)
* Node.js 0.10.36 ([EPEL](https://fedoraproject.org/wiki/EPEL))
* [SCL](https://www.softwarecollections.org/)
    - [rh-php56](https://www.softwarecollections.org/en/scls/rhscl/rh-php56/)
        - PHP 5.6.*
            - `rh-php56-php-cli`
            - `rh-php56-php-gd`
            - `rh-php56-php-mbstring`
            - `rh-php56-php-pdo`
        - PHP-FPM
            - `rh-php56-php-fpm`
    - [php56more](https://www.softwarecollections.org/en/scls/remi/php56more/)
        - Mcrypt
            - `more-php56-php-mcrypt`

Apache+mod_php で動作させる場合は、 `runtime` ディレクトリと `db/fest.sqlite` ファイルの権限（所有者とパーミッション）に注意してください。

CentOS 7 の標準 PHP は 5.4.16 です。このバージョンでは動作しません。


使い方
------

### PREREQUIREMENTS ###

イカモドキのウェブフォント生成のために `webify` コマンドが必要です。

[webifyのリリース](https://github.com/ananthakumaran/webify/releases)からコンパイル済みバイナリを取得するか、
`cabal install webify` で webify をインストールしてください。

CentOS 7 で EPEL が有効なら、こんな感じでインストールできるみたいです。

```sh
sudo yum install cabal-install
cabal update
cabal install webify
```

`webify` の実行ファイルへパスを通すことを忘れずに。(`~/.cabal/bin` あたり)

### SETUP ###

1. `git clone` します

    ```sh
    git clone https://github.com/fetus-hina/fest.ink.git fest.ink
    cd fest.ink
    ```

2. `make` します

    ```sh
    make
    ```

3. ウェブサーバとかを良い感じにセットアップするときっと動きます。

### FAVICON ###

fest.ink の favicon はフリーライセンスではありません。
利用許可を得ている場合は次のように生成できます。

1. ライセンスキーを受け取ります

2. `config/favicon.license.txt` を作成し、ライセンスキーだけをその中に記載し保存します

3. `make` あるいは `make favicon` します

    ```sh
    make
    ```

### FETCH DATA ###

任天堂から新しいデータを取得するには、定期的に `/path/to/yii official-data/update` を実行します。フェスが開催されていないときは何もしません。


### TWITTER ###

Twitter 連携機能を有効にするには次のように設定します。

1. 必要であれば新規 Twitter アカウントを取得します。
2. 取得したアカウント、または、あなたのアカウントで新しいアプリを申請し、 `consumer key` と `consumer secret` を取得します。
3. `config/twitter.php` を開き、`consumerKey` と `consumerSecret` にそれぞれ取得した値を設定します。 `userToken` と `userSecret` はこの時点では空にしておきます。
4. コマンドラインで認証を行います。

    ```sh
    ./yii twitter/auth
    ```

5. 表示される指示に従って URL にアクセスし、取得したアカウントで認証します。認証すると PIN コードが表示されますのでコマンドラインにそのまま打ち込みます。
6. PIN コードの確認が行われた後、 `userToken` と `userSecret` に設定するべき値が表示されますので、 `config/twitter.php` に設定します。
7. データを収集したあと次のように実行すればツイートされます。実際には `cron` 等を設定することになります。ツイート内容は現在固定です。 `commands/TwitterController.php` を開いて該当箇所を確認してください。

    ```sh
    ./yii twitter/update
    ```


API
---

fest.ink からデータを取得する API は次のページを参照してください。
[https://fest.ink/api](https://fest.ink/api)


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


ライセンス（イラスト）
----------------------

Copyright (C) 2015 AIZAWA Hina \<hina@bouhime.com\>

Copyright (C) 2015 Chomado

The artwork of Inkling-Girl is NON-FREE License.

Please contact us if you want to get a license.
