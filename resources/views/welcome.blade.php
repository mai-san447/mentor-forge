{{--
    未ログインで最初に見られるページ。ダッシュボードと同じ配色・語彙にそろえている。
    スタイルをこのファイルに内包しているのは、Vite のビルド成果物に依存させないため
    （このページだけ CSS のリビルド漏れで崩れる事故を避ける）。Laravel 標準の welcome も同じ作り。
--}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mentor Forge — 学んで、試して、実践する</title>
    <meta name="description" content="メンタリングの知識をクイズで学び、ソロ練習とトリオ練習で身につける学習アプリ。">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", "Hiragino Sans",
                         "Noto Sans JP", Meiryo, sans-serif;
            color: #18181b;
            background: #fafafa;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }
        .wrap { max-width: 1000px; margin: 0 auto; padding: 0 24px; }

        header { border-bottom: 1px solid #e4e4e7; background: #fff; }
        .head-inner { display: flex; align-items: center; justify-content: space-between;
                      gap: 16px; padding: 18px 0; flex-wrap: wrap; }
        .brand { font-size: 13px; font-weight: 700; letter-spacing: 0.2em; color: #0d9488; }
        .nav { display: flex; gap: 12px; align-items: center; }

        .btn { display: inline-block; padding: 10px 20px; border-radius: 9999px;
               font-size: 15px; font-weight: 700; transition: background .15s, color .15s; }
        .btn-primary { background: #0f766e; color: #fff; }
        .btn-primary:hover { background: #115e59; }
        .btn-ghost { color: #0f766e; border: 1px solid #99f6e4; background: #fff; }
        .btn-ghost:hover { background: #f0fdfa; }

        .hero { padding: 72px 0 56px; }
        .hero h1 { margin: 12px 0 0; font-size: clamp(28px, 5vw, 44px);
                   font-weight: 800; letter-spacing: -0.01em; line-height: 1.35; }
        .hero p { margin: 16px 0 0; max-width: 40em; font-size: 17px; color: #52525b; }
        .hero .cta { margin-top: 32px; display: flex; gap: 12px; flex-wrap: wrap; }

        .cards { display: grid; gap: 20px; grid-template-columns: 1fr; padding-bottom: 56px; }
        @media (min-width: 760px) { .cards { grid-template-columns: repeat(3, 1fr); } }
        .card { border-radius: 16px; padding: 24px; color: #fff;
                box-shadow: 0 1px 2px rgba(0,0,0,.06); }
        .card .icon { font-size: 30px; line-height: 1; }
        .card h2 { margin: 18px 0 0; font-size: 20px; font-weight: 700; }
        .card p { margin: 8px 0 0; font-size: 14px; }
        .c-solo { background: #0f766e; }
        .c-solo p { color: #f0fdfa; }
        .c-trio { background: #4338ca; }
        .c-trio p { color: #eef2ff; }
        .c-quiz { background: #f59e0b; }
        .c-quiz p { color: #fffbeb; }

        .note { border: 1px solid #e4e4e7; background: #fff; border-radius: 16px;
                padding: 24px; margin-bottom: 64px; }
        .note h3 { margin: 0; font-size: 16px; font-weight: 700; }
        .note p { margin: 8px 0 0; font-size: 15px; color: #52525b; }

        footer { border-top: 1px solid #e4e4e7; background: #fff;
                 padding: 24px 0; font-size: 13px; color: #71717a; }
    </style>
</head>
<body>
    <header>
        <div class="wrap head-inner">
            <span class="brand">MENTOR FORGE</span>
            <nav class="nav">
                @auth
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">ダッシュボードへ</a>
                @else
                    <a class="btn btn-ghost" href="{{ route('login') }}">ログイン</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">新規登録</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="wrap">
        <section class="hero">
            <h1>学んで、試して、実践する</h1>
            <p>
                メンタリングの知識をクイズで学び、ソロ練習とトリオ練習で身につけるための学習アプリです。
                知識を入れて終わりにせず、対話を実際にやってみるところまでを一つの流れにしています。
            </p>
            <div class="cta">
                @auth
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">ダッシュボードへ</a>
                @else
                    <a class="btn btn-primary" href="{{ route('register') }}">新規登録して始める</a>
                    <a class="btn btn-ghost" href="{{ route('login') }}">ログイン</a>
                @endauth
            </div>
        </section>

        <section class="cards">
            <div class="card c-solo">
                <span class="icon">🎧</span>
                <h2>ソロ練習</h2>
                <p>AIペルソナと一人で対話を練習します。</p>
            </div>
            <div class="card c-trio">
                <span class="icon">👥</span>
                <h2>トリオ練習</h2>
                <p>3人で役割を交代しながら実践します。</p>
            </div>
            <div class="card c-quiz">
                <span class="icon">🧠</span>
                <h2>クイズ</h2>
                <p>傾聴や質問の基礎を確認します。</p>
            </div>
        </section>

        @guest
            <section class="note">
                <h3>はじめての方へ</h3>
                <p>
                    新規登録するとすぐに全機能をお試しいただけます。確認メールの受信は不要です。
                </p>
            </section>
        @endguest
    </main>

    <footer>
        <div class="wrap">Mentor Forge</div>
    </footer>
</body>
</html>
