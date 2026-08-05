#!/usr/bin/env bash
# さくらレンタルサーバ向けのデプロイアーカイブを作る。
# 手順の全体は docs/deploy-sakura.md を参照。
#
# 本番PHPは 8.3 が上限で、8.4 必須の依存が lock に入ると HTTP 500 になる。
# また Sail でコンパイルされた Blade / config キャッシュは絶対パスを焼き込んでいて
# 本番で壊れる。過去に両方やらかしているので、ここで機械的に検査して止める。
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
REPO_NAME="$(basename "$REPO_ROOT")"
PARENT_DIR="$(dirname "$REPO_ROOT")"
DATE="$(date +%Y-%m-%d)"
OUT="${PARENT_DIR}/mentor-forge-${DATE}.tar.gz"

cd "$REPO_ROOT"

die() { echo "ERROR: $*" >&2; exit 1; }
ok()  { echo "  [ok] $*"; }

echo "==> 事前チェック"

# アーカイブのトップ階層名がそのまま展開先の名前になる。
[ "$REPO_NAME" = "mentor-forge" ] \
  || die "リポジトリのディレクトリ名が mentor-forge ではない（現在: ${REPO_NAME}）。展開先の名前がずれる。"
ok "ディレクトリ名 mentor-forge"

# 原則1: platform 固定。これが外れると 8.4 必須の依存が入り込む。
PLATFORM_PHP="$(php -r '$j = json_decode(file_get_contents("composer.json"), true); echo $j["config"]["platform"]["php"] ?? "";')"
[ "$PLATFORM_PHP" = "8.3.0" ] \
  || die "composer.json の config.platform.php が 8.3.0 ではない（現在: '${PLATFORM_PHP}'）。さくらの PHP 上限は 8.3。"
ok "config.platform.php = 8.3.0"

# ビルドに使う PHP 自体も 8.3 系でないと、生成された autoload が本番とずれる。
PHP_MM="$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')"
[ "$PHP_MM" = "8.3" ] \
  || die "php が ${PHP_MM} 系（$(command -v php)）。8.3 系でビルドすること。"
ok "ビルド用 php = $(php -r 'echo PHP_VERSION;')"

# 原則2の実効チェック: --ignore-platform-req で誤魔化していたらここで落ちる。
composer check-platform-reqs --no-dev --quiet \
  || die "本番PHP(8.3)で満たせない依存がある。composer.json を直して composer update し直すこと。--ignore-platform-req で回避しない。"
ok "composer check-platform-reqs --no-dev"

# Vite のビルド成果物は .gitignore 済みなので、無ければ画面が崩れる。
[ -f public/build/manifest.json ] \
  || die "public/build/manifest.json が無い。先に './vendor/bin/sail npm run build' を実行すること。"
ok "public/build/manifest.json"

echo "==> 本番用 vendor を生成（dev 依存を除外）"
composer install --no-dev --optimize-autoloader --no-interaction --quiet

# 失敗しても成功しても、ローカルの dev 依存（pest/pint/larastan）は必ず戻す。
restore_dev_deps() {
  echo "==> ローカルの dev 依存を復元"
  composer install --no-interaction --quiet || echo "WARN: dev 依存の復元に失敗。手動で 'composer install' を実行すること。" >&2
}
trap restore_dev_deps EXIT

# vendor/ は .gitignore 対象で composer が作り直すため、遮断用の .htaccess が消える。
# アプリ本体が公開領域の中にある以上ここが空くと vendor が丸ごと読めるので、毎回置き直す。
# 中身はリポジトリ管理下の app/.htaccess と同一（本番で403が出ることを実機確認済みの版）。
cp app/.htaccess vendor/.htaccess

echo "==> アーカイブ作成: ${OUT}"
rm -f "$OUT"
tar -czf "$OUT" -C "$PARENT_DIR" \
  --exclude="${REPO_NAME}/.git" \
  --exclude="${REPO_NAME}/.github" \
  --exclude="${REPO_NAME}/node_modules" \
  --exclude="${REPO_NAME}/tests" \
  --exclude="${REPO_NAME}/.env" \
  --exclude="${REPO_NAME}/.env.backup" \
  --exclude="${REPO_NAME}/database/database.sqlite" \
  --exclude="${REPO_NAME}/storage/framework/views/*.php" \
  --exclude="${REPO_NAME}/storage/framework/cache/data/*" \
  --exclude="${REPO_NAME}/storage/framework/sessions/*" \
  --exclude="${REPO_NAME}/storage/logs/*.log" \
  --exclude="${REPO_NAME}/bootstrap/cache/*.php" \
  --exclude='*.orig' \
  "$REPO_NAME"

echo "==> 中身の検証"
LIST="$(tar -tzf "$OUT")"

# grep はヒアストリングで渡す。パイプにすると grep -q が先に終了して
# writer が SIGPIPE で死に、pipefail が「不一致」と誤判定する。
check_absent() {
  if grep -qE "$1" <<< "$LIST"; then
    grep -E "$1" <<< "$LIST" | head -5 >&2
    die "$2"
  fi
  ok "$2 — 含まれていない"
}

check_absent "^${REPO_NAME}/storage/framework/views/.*\.php$" "コンパイル済み Blade（絶対パスを焼き込んでいる）"
check_absent "^${REPO_NAME}/bootstrap/cache/.*\.php$"          "config/route キャッシュ"
check_absent "^${REPO_NAME}/\.env$"                            ".env（本番の値を上書きしないため）"
check_absent "^${REPO_NAME}/node_modules/"                     "node_modules"

for required in "${REPO_NAME}/public/index.php" "${REPO_NAME}/vendor/autoload.php" \
                "${REPO_NAME}/public/build/manifest.json"; do
  grep -qxF "$required" <<< "$LIST" || die "${required} がアーカイブに無い。"
  ok "${required} を含む"
done

# 遮断用 .htaccess の欠落は、気づかないまま .env や vendor が公開される事故に直結する。
# 1枚でも欠けたらアーカイブを不良品として扱う。
for guarded in "" "/app" "/bootstrap" "/config" "/database" "/resources" "/routes" "/storage" "/vendor"; do
  grep -qxF "${REPO_NAME}${guarded}/.htaccess" <<< "$LIST" \
    || die "${REPO_NAME}${guarded}/.htaccess がアーカイブに無い。公開領域が空く。"
done
ok "遮断用 .htaccess 9枚をすべて含む"

echo
echo "完了: ${OUT} ($(du -h "$OUT" | cut -f1))"
echo
# 接続先はリポジトリに書かない（public リポジトリのため）。
# 事前に `export DEPLOY_SSH=<user>@<host>` しておくとコピペできる形で出る。
SSH_TARGET="${DEPLOY_SSH:-<user>@<host>}"

echo "次の手順（詳細は docs/deploy-sakura.md）:"
echo "  scp ${OUT} ${SSH_TARGET}:~/"
echo "  ssh ${SSH_TARGET}"
echo "  mkdir -p ~/backup-${DATE}"
echo "  cp ~/www/mentor-forge/.env ~/backup-${DATE}/env.mentor-forge"
echo "  mv ~/www/mentor-forge ~/backup-${DATE}/mentor-forge"
echo "  cd ~/www && tar xzf ~/$(basename "$OUT") && ls -d ~/www/mentor-forge/public"
