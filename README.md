[![license][license]][license-url]

# WP BASE THEME

WordPressでテーマ開発を行うときのベースです。
scssとjsはwebpack、画像はshrapを利用しています。

## 始め方

1. `themes`ディレクトリにクローンまたは [ダウンロード](https://github.com/SenaMurakami/wp_base_theme/archive/refs/heads/master.zip "Download the WP Base Theme Zip") してください。`(./wp-content/themes)`
```
git clone https://github.com/SenaMurakami/wp_base_theme.git <your_theme_name>
```
2. テーマディレクトリに移動
```
cd <your_theme_name>
```
3. 必要なパッケージをインストール
```
npm ci
```
4. `npm run dev` コマンドで開発開始
5. `npm run build:prod` は本番アップ用
