# 開発環境セットアップ

最終更新: 2026-09-08

## このリポジトリの位置づけ

`mentor-forge` は Laravel / Livewire / MySQL / Vite で構成された既存アプリです。
本番はさくらレンタルサーバで稼働しています。Vercel は Next.js や静的な
Viteアプリの公開には便利ですが、このLaravelアプリをそのまま移す先ではありません。

将来 Next.js + Supabase + Vercel、または React + Cloudflare の構成を試す場合は、
本番を壊さないよう別リポジトリで検証します。

## 推奨バージョン

- PHP 8.3
- Composer 2
- Node.js 22
- npm（`package-lock.json`を使用）
- Git

`.php-version` と `.nvmrc` をリポジトリに置いているため、対応ツールでは自動的に
推奨バージョンが選ばれます。

## 初回セットアップ

```bash
git clone https://github.com/mai-san447/mentor-forge.git
cd mentor-forge
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
```

MySQLを使う開発環境では、SQLiteの代わりにローカル専用DBを設定します。本番の
`.env` やDB認証情報はコピーしません。

## 日常の作業

```bash
git switch main
git pull --ff-only
git switch -c feature/短い作業名
```

AIに実装を依頼する前に、リポジトリ直下の `AGENTS.md` を読ませます。Claude Codeは
`CLAUDE.md` から同じルールを参照します。

変更後:

```bash
composer ci:check
npm run build
git status
git add <確認したファイルだけ>
git commit -m "変更内容"
git push -u origin <ブランチ名>
```

GitHubでPull Requestを作ると、既存のGitHub ActionsがPHP 8.3・Node.js 22で
ビルド、コードスタイル、静的解析、Feature/Unitテストを実行します。

## AI同士のレビュー

最初は完全自動で修正まで行わせず、次の分担にします。

1. 実装担当AIがfeatureブランチで変更する
2. GitHub Actionsが機械的な検査を行う
3. 別のAIがPull Requestの差分を `AGENTS.md` の順序でレビューする
4. 人が指摘を確認してから修正・マージする

Claude Codeでレビューする例:

```text
AGENTS.mdに従い、mainとの差分をレビューしてください。ファイルは変更せず、
重大度順に、該当ファイル、問題、理由、修正案を示してください。
```

Codexにも同じ依頼を出し、両者の指摘が一致した箇所から対応します。AIがAIの指摘を
無制限に修正し続けるループは、誤修正や費用増加を招くため初期段階では使いません。

## デプロイ

現行本番の手順は [`docs/deploy-sakura.md`](deploy-sakura.md) を参照してください。
本番への自動デプロイは、秘密情報、バックアップ、DBマイグレーション、ロールバックを
含むため、CIと分離したうえで別途設計します。現時点ではGitHubへのpushだけで本番を
変更しません。
