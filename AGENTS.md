# AGENTS.md - stat.ink

## AI エージェントへの指示

- 思考は英語で行い、ユーザーへの応答は日本語で記載すること
- 変更前にしっかりとコードを読み、既存の実装を十分に理解してから作業に取りかかること
- ヘルパー関数などのテスト可能な純粋関数を作成するときは、TDD に従いテストを先に作成してから実装に取りかかること。テストは網羅性を追わず、本質的な項目 (関数の責務・想定される代表的な入出力・境界条件・エラー条件) に絞って実施すること

## プロジェクト概要

stat.ink は Splatoon シリーズ (1/2/3) のバトル統計を記録・分析する Web アプリケーション。外部ツール (s3s, IkaLog, SquidTracks 等) から API 経由でバトル結果を受け取り、統計情報を表示する。

## 技術スタック

- **フレームワーク**: Yii 2 (PHP)
- **言語**: PHP 8.5+ (8.5 の構文機能を使用)
- **データベース**: PostgreSQL 15+
- **フロントエンド**: jQuery 3, Bootstrap 3, React 18, Redux, Chart.js, SCSS
- **ビルド**: GNU Make, Webpack (React), Babel, PostCSS, SASS
- **テスト**: Codeception 5
- **ライセンス**: MIT

## 主要コマンド

```bash
make init              # 初期セットアップ (依存関係 + マイグレーション + アセット)
make init-no-resource  # フロントエンドビルドなしのセットアップ
make resource          # フロントエンドアセットの全ビルド
make react             # React コンポーネントのビルド (webpack)
make test              # Codeception テスト実行
make migrate-db        # データベースマイグレーション実行
make check-style       # 全リンター実行 (PHP + JS + CSS)
make check-style-php   # PHP CodeSniffer のみ
make check-style-js    # ESLint (neostandard) のみ
make check-style-css   # StyleLint のみ
make fix-style         # コードスタイルの自動修正
make phpstan           # PHPStan 静的解析
make clean             # 生成ファイルの削除
./yii i18n/check-keys  # Yii::t() のキーが翻訳カタログ (ja) に存在するか、訳文の ICU プレースホルダが正しいか検査
```

## ディレクトリ構成

```
actions/       # Yii スタンドアロンアクションクラス (機能別に整理)
assets/        # Yii アセットバンドル定義
commands/      # コンソールコマンド (./yii <command>)
components/    # 再利用可能なコンポーネント (ヘルパー, バリデータ, ウィジェット, ジョブ)
config/        # アプリケーション設定 (web.php, console.php, db.php 等)
controllers/   # MVC コントローラ
data/          # 静的データファイル, パッチ
messages/      # i18n 翻訳ファイル
migrations/    # データベースマイグレーション (Yii 形式: m{timestamp}_{name}.php)
models/        # ActiveRecord モデル, フォームモデル
resources/     # フロントエンドソース (JS, SCSS, React, 画像)
tests/         # Codeception テストスイート
views/         # PHP ビューテンプレート (2スペースインデント, phpcs 対象外)
web/           # Web ドキュメントルート
```

## コーディング規約

### PHP
- **規約**: JP3CKI (PSR-12 ベースのカスタム規約)
- **ツール**: `vendor/bin/phpcs` (`phpcs.xml` で設定)
- 4スペースインデント
- レガシーコード: 緩い比較 (`==`) は許容されている
- マイグレーションと `components/ability/` では非キャメルケースのクラス名を許可
- ビューは phpcs 対象外 (2スペースインデント)
- オートロード名前空間: `app\` がプロジェクトルートに対応

### JavaScript
- **規約**: neostandard (Standard Style 後継 + セミコロン)
- グローバル変数: `$`, `jQuery`

### CSS/SCSS
- **規約**: sass-guidelines (StyleLint, `.stylelintrc.json` で設定)

## Git ワークフロー

- **本番ブランチ**: `master`
- **開発ブランチ**: `dev` (PR はここに向ける)
- GitHub Actions で push / PR 時に CI 実行
- CI スキップ: コミットメッセージに `[ci skip]`、PHP チェックのみスキップは `[no php]`

## CI パイプライン

PHP 8.5, 8.6 (8.7 は実験的) と PostgreSQL 15, 18, 19 でテスト。チェック項目:
1. PHP 構文チェック
2. PHP コーディングスタイル (phpcs)
3. Author チェック
4. composer.json の正規化
5. JS コーディングスタイル (ESLint + neostandard)
6. CSS コーディングスタイル (stylelint)
7. ライセンス準拠チェック
8. パッケージセキュリティ監査
9. フルビルド + マイグレーション + Codeception テスト

## 開発メモ

- エントリポイント: `web/index.php` (Web), `./yii` (コンソール)
- キューシステムによる非同期ジョブ処理 (バトル統計計算等)
- 多言語対応 (i18n)
- 推奨開発環境: [Vagrant devenv](https://github.com/statink/devenv-vagrant)
- Docker 環境は存在するが非推奨
