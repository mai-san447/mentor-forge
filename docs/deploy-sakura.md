# さくらレンタルサーバ デプロイ／復旧手順（mentor-forge）

最終更新: 2026-08-05

公開URL: https://<host>/mentor-forge/public/

> `<host>` `<user>` は実際の値に読み替えること。
> **このリポジトリは public なので、さくらのホスト名とユーザー名は書かない。**
> シェルで `export DEPLOY_SSH=<user>@<host>` しておくと、
> `scripts/build-deploy-archive.sh` が次の手順をコピペできる形で出力する。

---

## 0. 環境の事実（ここを間違えると必ず500になる）

| 項目 | 値 |
| --- | --- |
| 本番PHP | **8.3 が上限**（`/usr/local/php/` は 5.2〜8.3 のみ。`php` は 8.3.32） |
| 本番シェル | FreeBSD の **csh**。`export` は使えない（`setenv`）。**`for` ループも使えない**ので `find … \| xargs …` で代用する |
| 接続 | **公開鍵認証を登録済み。`ssh sakura` だけで繋がる**（`~/.ssh/config` にホスト名とユーザー名を記載）。パスワード入力は不要 |
| アプリ本体 | `~/www/mentor-forge/`（公開領域 `www` の中にアプリ全体がある） |
| 同居プロジェクト | `~/www` には `ouen_plz` `reading_log` などが同居。**一括の `mv`／`rm` は絶対にしない** |
| ローカル | WSL Ubuntu `~/mentor-forge`。ホストのPHPも 8.3 系、composer あり |

---

## 1. 絶対に守る4原則（再発防止の本体）

1. **`composer.json` の `config.platform.php = 8.3.0` を外さない。**
   これが無いと PHP 8.4 必須のパッケージ（Symfony 8.1 系、Pest 5 系）が `composer.lock` に入り、本番が HTTP 500 になる。
2. **`--ignore-platform-req=php` は使わない。** エラーを黙らせるだけで、問題を本番に持ち越す。
3. **`storage/framework/views/` はアーカイブに入れない。**
   コンパイル済み Blade が Sail の絶対パス `/var/www/html/...` を焼き込んでおり、本番で 500 の原因になる。
   同じ理由で `bootstrap/cache/*.php`（config/route キャッシュ）も入れない。
4. **アーカイブはトップに `mentor-forge/` を含む形式。展開先は必ず `~/www`。**
   - `~/www/mentor-forge` の中で解く → `mentor-forge/mentor-forge/` の二重になる
   - prefix 無しのアーカイブを `~/www` で解く → `www` 直下にバラまかれる（他PJと混ざる）
   - 過去に**両方やらかしている**。展開後に `ls -d ~/www/mentor-forge/public` で必ず確認する。

---

## 2. 通常デプロイ／復旧の手順

### 2-1. ローカル: アーカイブを作る

アセットを変更した場合のみ、先にビルドしておく（Sail のコンテナ内 node を使う）:

```bash
cd ~/mentor-forge
./vendor/bin/sail npm run build
```

アーカイブ作成（ガード付き。原則1〜4を機械的に検査する）:

```bash
cd ~/mentor-forge
./scripts/build-deploy-archive.sh
# → ~/mentor-forge-YYYY-MM-DD.tar.gz が出来る
```

このスクリプトは以下を満たさないと**その場で止まる**:

- `config.platform.php` が `8.3.0` である
- ビルドに使う `php` が 8.3 系である
- `composer check-platform-reqs --no-dev` が通る（＝本番PHPで動く依存だけになっている）
- `public/build/manifest.json` がある（Vite ビルド済み）
- 生成物に `.env`・コンパイル済み Blade・`bootstrap/cache/*.php` が**入っていない**

### 2-2. 転送

```bash
scp ~/mentor-forge-2026-08-05.tar.gz <user>@<host>:~/
```

### 2-3. サーバ: 退避（`~/www` の外へ）

```sh
ssh <user>@<host>

mkdir -p ~/backup-2026-08-05
cp ~/www/mentor-forge/.env ~/backup-2026-08-05/env.mentor-forge   # 先に .env を確保する
mv ~/www/mentor-forge ~/backup-2026-08-05/mentor-forge            # 対象を明示。ワイルドカード禁止
```

> `.env` はアーカイブに含めていない（本番の値をローカルで上書きしないため）。
> 退避のときに必ず控えを取ること。ここを飛ばすと `APP_KEY` を失い、セッションと暗号化データが壊れる。

### 2-4. サーバ: 展開

```sh
cd ~/www
tar xzf ~/mentor-forge-2026-08-05.tar.gz

ls -d ~/www/mentor-forge/public          # ← ここが出れば正しい階層
ls -d ~/www/mentor-forge/mentor-forge    # ← これが出たら二重。やり直し
```

### 2-5. サーバ: 環境と永続データを戻す

```sh
cp ~/backup-2026-08-05/env.mentor-forge ~/www/mentor-forge/.env
cp -R ~/backup-2026-08-05/mentor-forge/storage/app/. ~/www/mentor-forge/storage/app/

chmod -R 755 ~/www/mentor-forge/storage ~/www/mentor-forge/bootstrap/cache
```

`.env` は最低限これを満たすこと:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<host>/mentor-forge/public
APP_KEY=（退避した値をそのまま）
```

### 2-6. サーバ: キャッシュ再生成とマイグレーション

```sh
cd ~/www/mentor-forge
php -v                       # 8.3.32 であることを確認
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

### 2-7. 検証（ローカルから）

```bash
curl -sI https://<host>/mentor-forge/public/            | head -1  # → 200
curl -sI https://<host>/mentor-forge/.env               | head -1  # → 403
curl -sI https://<host>/mentor-forge/composer.json      | head -1  # → 403
curl -sI https://<host>/mentor-forge/vendor/autoload.php | head -1 # → 403
curl -sI https://<host>/mentor-forge/storage/logs/laravel.log | head -1  # → 403
```

**403 が1つでも 200/500 になったら、公開を止めて先に遮断を直す。**

---

## 3. ロールバック（1分で戻す）

```sh
mv ~/www/mentor-forge ~/www/mentor-forge.failed-2026-08-05
mv ~/backup-2026-08-05/mentor-forge ~/www/mentor-forge
cd ~/www/mentor-forge && php artisan config:clear && php artisan config:cache
```

退避ディレクトリ `~/backup-YYYY-MM-DD/` は **1〜2週間後に削除**する（`~/www` の外なので公開はされない）。

---

## 4. HTTP 500 が出たときの診断順

`APP_DEBUG=false` なので画面には何も出ない。ログを見る。

```sh
tail -50 ~/www/mentor-forge/storage/logs/laravel.log
```

原因の頻度順:

| 症状・ログ | 原因 | 対処 |
| --- | --- | --- |
| `syntax error, unexpected token` / 型構文エラーが vendor 配下で出る | PHP 8.4 必須の依存が lock に入った | ローカルで `composer check-platform-reqs --no-dev`。落ちたら `config.platform.php` を確認して `composer update` し直す |
| `/var/www/html/...` を含むパスが出る | Sail のコンパイル済み Blade / config キャッシュを持ち込んだ | サーバで `php artisan view:clear && php artisan config:clear`。次回から必ず除外する（原則3） |
| `Permission denied` / `failed to open stream` | `storage` `bootstrap/cache` の権限 | `chmod -R 755` |
| `No application encryption key` | `.env` の `APP_KEY` を失った | 退避した `.env` から戻す |
| 404 になる | 展開階層のミス（二重／バラまき） | `ls -d ~/www/mentor-forge/public` で確認し、2-3 からやり直す |

---

## 5. 既知の負債（直したら消す）

- **アプリ本体が公開領域 `www` の中にある。** これが根本原因。本来は `public/` だけを公開領域に置く構成が正しく、
  さくらのコントロールパネルでサブドメインを作り公開フォルダに `www/mentor-forge/public` を指定すれば解消する。
  そうすれば URL から `/public/` も消える。

  それまでの遮断策として、**`.htaccess` を9枚**置いている（リポジトリ管理）。
  ルートに1枚（`Options -Indexes` ＋ ドットファイル・`composer.*`・`artisan` を拒否）と、
  `app` `bootstrap` `config` `database` `resources` `routes` `storage` `vendor` の各直下に
  `Require all denied` を1枚ずつ。mod_rewrite に依存しないので、サブディレクトリが独自の
  RewriteRule を持っていても効く。**この方式は本番の Apache で 403 を実測確認している。**

  `vendor/` は `.gitignore` 対象で composer が作り直すため、`scripts/build-deploy-archive.sh` が
  アーカイブ作成時に `app/.htaccess` から複製し、9枚そろっていなければアーカイブを不良品として止める。
  1枚でも欠けると `.env` や `vendor` が URL で読めるようになるため、ここは検査で担保している。
