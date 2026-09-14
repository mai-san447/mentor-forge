# 旧版Vercelの不要な自動ビルドを停止

作業日: 2026年9月15日（日本時間）

## 理由

新版は別リポジトリ `mentor-forge-next` のNext.js / Vercelで公開済みです。こちらのLaravel版をVercelのNodeビルドとして動かす必要はありません。

ローカルの `npm run build` でも、PHP依存関係がない状態では `vendor/livewire/flux/dist/flux.css` の解決に失敗することを再現しました。過去のVercel失敗通知のすべてを同じ原因と断定するものではありません。

## 変更

- `vercel.json` に `git.deploymentEnabled: false` を追加。
- READMEに旧版・新版・データ移行範囲を明記。
- アプリのコード・本番DB・さくらの公開配置は変更しない。
- GitHub Actionsの品質検査を停止しない。

設定はVercel公式のGit自動デプロイ停止仕様に従います。既存の公開版やデータを削除する操作ではありません。

公式仕様: https://vercel.com/docs/project-configuration/git-configuration#turning-off-all-automatic-deployments

## 検証

- `composer ci:check`: この実行環境にComposerがなく実行できず。
- `npm run build`: PHP依存関係がないため、上記のFlux CSS解決エラー。
- このためローカルの旧版全体をテスト済みとは扱わない。PR上の、PHP依存関係を備える既存CIの結果を確認する。
- JSONの構文と変更対象は確認済み。

## 戻し方

このPRをRevertすると旧版のVercel Git自動デプロイ設定は元に戻ります。さくらの既存本番環境に変更はありません。
