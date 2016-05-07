`apikey`
========

`apikey` は認証が必要な API においてユーザを特定し、サービスを提供するためのキーになる項目です。

`apikey` は次の制約を満たします。

* `apikey` は必ず 43 文字です。
* `apikey` は必ず A-Z, a-z, 0-9 および "-" "_" で構成されます。
* `apikey` は必ず別のユーザと重複しません。

`apikey` の簡易的な検証には、次のような正規表現が利用できます。

```
/^[0-9A-Za-z_-]{43}$/
```

`apikey` は現在の実装上は次の制約を満たしますがこれに依存しないことを推奨します。

* `apikey` の最後の文字は次のいずれかとなります。 `0 4 8 A E I M Q U Y c g k o s w`
* `apikey` はユーザごとに一つです。

`apikey` 実装の詳細が必要であれば `app\models\User::generateNewApiKey()` を確認してください。

`apikey` はパスワードに相当する機密性の高い情報です。取り扱いには注意してください。


----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
