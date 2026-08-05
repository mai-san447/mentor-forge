<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{--
    @fluxAppearance を意図的に外している。

    アプリ画面は明色前提で書かれていて（body は bg-zinc-50、カードは bg-white）、
    dark: 変種も明示的な文字色も持っていない。外観がダークになると文字色だけが明るくなり、
    白背景に白文字で読めなくなる。既定はOS追従のため、OSをダークにしている人は全員この状態だった。

    正しい直し方は各ビューに dark: 変種を足すことだが、それには CSS の再ビルドが要る。
    表示不能のほうが重いので、まずライト固定にする。
    この行を戻すときは、先に dark: 対応を入れること。設定画面の「外観」はこの状態では効かない。
--}}
