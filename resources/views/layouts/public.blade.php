{{--
    未ログインでも見られる公開ページ（トップ・このサイトについて・お問い合わせ）の共通レイアウト。

    スタイルをここに内包しているのは、Vite のビルド成果物に依存させないため。
    公開ページはアプリ本体より変更頻度が低く、CSS のリビルド漏れでここだけ崩れる事故を避けたい。
    アプリ内の画面（ダッシュボード以降）は従来どおり Tailwind + Flux を使う。
--}}
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mentor Forge')</title>
    <meta name="description" content="@yield('description', 'メンタリングの知識をクイズで学び、ソロ練習とトリオ練習で身につける学習アプリ。')">

    <link rel="icon" href="{{ url('/favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ url('/apple-touch-icon.png') }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", "Hiragino Sans",
                         "Noto Sans JP", Meiryo, sans-serif;
            color: #18181b;
            background: #fafafa;
            line-height: 1.8;
            -webkit-font-smoothing: antialiased;
        }
        a { color: #0f766e; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 0 20px; }

        /* ヘッダー */
        header { border-bottom: 1px solid #e4e4e7; background: #fff; }
        .head-inner { display: flex; align-items: center; justify-content: space-between;
                      gap: 12px; padding: 16px 0; flex-wrap: wrap; }
        .brand { font-size: 13px; font-weight: 700; letter-spacing: 0.2em;
                 color: #0d9488; text-decoration: none; }
        .nav { display: flex; gap: 10px; align-items: center; }

        .btn { display: inline-block; padding: 10px 20px; border-radius: 9999px;
               font-size: 15px; font-weight: 700; text-decoration: none;
               border: 1px solid transparent; cursor: pointer;
               transition: background .15s, color .15s; }
        .btn-primary { background: #0f766e; color: #fff; }
        .btn-primary:hover { background: #115e59; }
        .btn-ghost { color: #0f766e; border-color: #99f6e4; background: #fff; }
        .btn-ghost:hover { background: #f0fdfa; }

        /* 見出し */
        h1 { font-size: clamp(26px, 5vw, 40px); font-weight: 800;
             letter-spacing: -0.01em; line-height: 1.4; margin: 12px 0 0; }
        h2 { font-size: 22px; font-weight: 700; margin: 0 0 16px;
             padding-bottom: 10px; border-bottom: 2px solid #0d9488; display: inline-block; }
        h3 { font-size: 17px; font-weight: 700; margin: 0; }
        section { padding: 40px 0; }
        section p { color: #3f3f46; }

        /* ヒーロー */
        .hero { padding: 60px 0 40px; }
        .hero .eyebrow { font-size: 13px; font-weight: 700; letter-spacing: 0.2em; color: #0d9488; }
        .hero p.lead { font-size: 17px; max-width: 42em; }
        .cta { margin-top: 28px; display: flex; gap: 12px; flex-wrap: wrap; }

        /* 機能カード */
        .cards { display: grid; gap: 16px; grid-template-columns: 1fr; }
        @media (min-width: 720px) { .cards { grid-template-columns: repeat(3, 1fr); } }
        .card { border-radius: 16px; padding: 22px; color: #fff;
                box-shadow: 0 1px 2px rgba(0,0,0,.06); }
        .card .icon { font-size: 28px; line-height: 1; }
        .card h3 { margin-top: 14px; font-size: 19px; }
        .card p { margin: 6px 0 0; font-size: 14px; line-height: 1.7; }
        .c-solo { background: #0f766e; } .c-solo p { color: #f0fdfa; }
        .c-trio { background: #4338ca; } .c-trio p { color: #eef2ff; }
        .c-quiz { background: #f59e0b; } .c-quiz p { color: #fffbeb; }

        /* 使い方ステップ */
        .steps { display: grid; gap: 16px; grid-template-columns: 1fr; counter-reset: step; }
        @media (min-width: 720px) { .steps { grid-template-columns: repeat(3, 1fr); } }
        .step { background: #fff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 22px; }
        .step .num { display: inline-flex; align-items: center; justify-content: center;
                     width: 32px; height: 32px; border-radius: 9999px;
                     background: #0f766e; color: #fff; font-weight: 700; font-size: 15px; }
        .step h3 { margin-top: 14px; }
        .step p { margin: 6px 0 0; font-size: 14px; line-height: 1.7; color: #52525b; }

        /* 箱 */
        .box { background: #fff; border: 1px solid #e4e4e7; border-radius: 16px; padding: 24px; }
        .box.warn { background: #fffbeb; border-color: #fde68a; }
        .box ul { margin: 0; padding-left: 1.2em; }
        .box li { margin: 6px 0; color: #3f3f46; }
        .box li:first-child { margin-top: 0; }

        /* FAQ */
        details { background: #fff; border: 1px solid #e4e4e7; border-radius: 12px;
                  padding: 16px 20px; margin-bottom: 10px; }
        details[open] { border-color: #99f6e4; }
        summary { cursor: pointer; font-weight: 700; list-style: none; }
        summary::-webkit-details-marker { display: none; }
        summary::before { content: "＋ "; color: #0d9488; font-weight: 700; }
        details[open] summary::before { content: "− "; }
        details p { margin: 10px 0 0; color: #52525b; font-size: 15px; }

        /* 定義リスト（動作環境など） */
        dl { margin: 0; }
        dt { font-weight: 700; margin-top: 14px; font-size: 15px; }
        dt:first-child { margin-top: 0; }
        dd { margin: 4px 0 0; color: #52525b; font-size: 15px; }

        /* フォーム */
        .field { margin-bottom: 18px; }
        .field label { display: block; font-weight: 700; font-size: 15px; margin-bottom: 6px; }
        .field .hint { font-weight: 400; color: #71717a; font-size: 13px; }
        .field input[type=text], .field input[type=email], .field textarea {
            width: 100%; padding: 12px 14px; font-size: 16px; font-family: inherit;
            border: 1px solid #d4d4d8; border-radius: 10px; background: #fff; color: inherit;
        }
        .field input:focus, .field textarea:focus {
            outline: 2px solid #0d9488; outline-offset: 1px; border-color: #0d9488;
        }
        .field textarea { min-height: 160px; resize: vertical; line-height: 1.7; }
        .error { color: #b91c1c; font-size: 14px; margin: 6px 0 0; }
        .notice { background: #f0fdfa; border: 1px solid #99f6e4; color: #115e59;
                  border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; }
        /* ボット対策。人には見えず、自動入力されたら弾く */
        .hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }

        /* 一覧テーブル */
        .table-scroll { overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; font-size: 14px; background: #fff; }
        th, td { border: 1px solid #e4e4e7; padding: 10px 12px; text-align: left; vertical-align: top; }
        th { background: #f4f4f5; font-weight: 700; white-space: nowrap; }
        td.body { white-space: pre-wrap; min-width: 20em; }

        /* フッター */
        footer { border-top: 1px solid #e4e4e7; background: #fff; margin-top: 40px;
                 padding: 28px 0; font-size: 14px; color: #71717a; }
        .foot-links { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="wrap head-inner">
            <a class="brand" href="{{ route('home') }}">MENTOR FORGE</a>
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
        @yield('content')
    </main>

    <footer>
        <div class="wrap">
            <div class="foot-links">
                <a href="{{ route('home') }}">トップ</a>
                <a href="{{ route('about') }}">このサイトについて</a>
                <a href="{{ route('contact.create') }}">お問い合わせ</a>
            </div>
            <div>制作: EMKO ／ 学習課題として制作したものです</div>
        </div>
    </footer>
</body>
</html>
