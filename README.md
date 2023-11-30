

# WP BASE THEME

WordPressでテーマ開発を行うときのベースです。
scssとjsはwebpack、画像はshrapを利用しています。

## インストール

1. `themes`ディレクトリにクローンまたは [ダウンロード](https://github.com/SenaMurakami/wp_base_theme/archive/refs/heads/master.zip "Download the WP Base Theme Zip") してください。`(./wp-content/themes)`
```
git clone https://github.com/SenaMurakami/wp_base_theme.git <your_theme_name>
```
2. テーマディレクトリに移動します。
```
cd <your_theme_name>
```
3. 必要なパッケージをインストールします。
```
npm ci
```

## 始め方
- 開発開始
```
npm run dev
```
- 本番アップ用
```
npm run build:prod
```
- 画像を全て圧縮
```
npm run sharp:all
```

## CSS設計

FLOCCSで設計。js関連のクラスは`js-`のようにプレフィックスをつけてください。
