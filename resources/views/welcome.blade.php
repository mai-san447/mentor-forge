@extends('layouts.public')

@section('title', 'Mentor Forge — 学んで、試して、実践する')

@section('content')
    <section class="hero">
        <p class="eyebrow">MENTOR FORGE</p>
        <h1>学んで、試して、実践する</h1>
        <p class="lead">
            メンタリング（1on1）のスキルを、知識を入れて終わりにせず、
            対話を実際にやってみるところまで一続きで練習できる学習アプリです。
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

    <section>
        <h2>Mentor Forge とは</h2>
        <p>
            後輩や部下との1on1を任されたものの、何をどう聞けばいいのか分からない。
            本を読んで頭では分かっても、いざ相手を前にすると言葉が出てこない。
            Mentor Forge は、その「分かる」と「できる」の間を埋めるために作りました。
        </p>
        <p>
            傾聴・質問・共感の基本をクイズで確認したあと、相談者役を相手に一人で対話を練習できます。
            さらに実際の3人で役割を交代しながら練習し、お互いにフィードバックを残せます。
            <strong>何度でも失敗できる場所</strong>で先に失敗しておくのが、このアプリの役割です。
        </p>
    </section>

    <section>
        <h2>3つの練習</h2>
        <div class="cards">
            <div class="card c-solo">
                <span class="icon">🎧</span>
                <h3>ソロ練習</h3>
                <p>相談者役と1対1でテキスト対話。終了すると対話の内容からスコアが出ます。</p>
            </div>
            <div class="card c-trio">
                <span class="icon">👥</span>
                <h3>トリオ練習</h3>
                <p>メンター・メンティ・観察者の3役で実践。終わったら相互にフィードバックを記録します。</p>
            </div>
            <div class="card c-quiz">
                <span class="icon">🧠</span>
                <h3>クイズ</h3>
                <p>傾聴・質問・共感の基本を4択で確認。1問ごとに解説が出ます。</p>
            </div>
        </div>
    </section>

    <section>
        <h2>使い方</h2>
        <div class="steps">
            <div class="step">
                <span class="num">1</span>
                <h3>登録する</h3>
                <p>メールアドレスとパスワードだけで登録できます。確認メールを待つ必要はありません。</p>
            </div>
            <div class="step">
                <span class="num">2</span>
                <h3>クイズで学ぶ</h3>
                <p>まずクイズで基本を確認します。数分で終わります。間違えた問題には解説が出ます。</p>
            </div>
            <div class="step">
                <span class="num">3</span>
                <h3>練習する</h3>
                <p>一人ならソロ練習へ。3人そろうならトリオ練習でルームを作り、コードを共有します。</p>
            </div>
        </div>
    </section>

    <section>
        <h2>動作環境</h2>
        <div class="box">
            <dl>
                <dt>対応端末</dt>
                <dd>パソコン・スマートフォン・タブレット。画面幅に合わせて表示が変わります。</dd>
                <dt>対応ブラウザ</dt>
                <dd>Google Chrome / Microsoft Edge / Safari / Firefox の最新版。</dd>
                <dt>必要なもの</dt>
                <dd>インターネット接続とメールアドレスのみ。アプリのインストールは不要です。</dd>
                <dt>トリオ練習</dt>
                <dd>3人それぞれの端末が必要です。同じルームコードを共有して参加します。</dd>
            </dl>
        </div>
    </section>

    <section>
        <h2>ご利用にあたって</h2>
        <div class="box warn">
            <ul>
                <li><strong>本サービスは練習を目的とした学習用アプリです。</strong>実際の相談や支援を受け付けるものではありません。</li>
                <li>練習相手として登場する相談者は<strong>実在の人物ではありません</strong>。あらかじめ用意した応答パターンに沿って返答します。</li>
                <li>心身の不調や深刻な悩みについては、医療機関や公的な相談窓口にご相談ください。</li>
                <li>学習課題として制作したものです。内容の正確性や、継続してご利用いただけることを保証するものではありません。</li>
            </ul>
        </div>
    </section>

    <section>
        <h2>よくあるご質問</h2>

        <details>
            <summary>費用はかかりますか？</summary>
            <p>かかりません。学習課題として公開しているもので、料金の請求や決済の仕組みはありません。</p>
        </details>

        <details>
            <summary>スマートフォンでも使えますか？</summary>
            <p>使えます。ブラウザからそのままご利用いただけます。アプリのインストールは不要です。</p>
        </details>

        <details>
            <summary>相談者役は本物の人間ですか？</summary>
            <p>いいえ。あらかじめ用意した応答パターンに沿って返答しています。こちらの言葉づかいによって返し方が変わるようにしてあり、指示的な言い方をすると相手が話しづらくなる、という反応も含まれています。</p>
        </details>

        <details>
            <summary>一人でも使えますか？</summary>
            <p>使えます。クイズとソロ練習は一人で完結します。トリオ練習だけは3人必要です。</p>
        </details>

        <details>
            <summary>トリオ練習には何人必要ですか？</summary>
            <p>3人です。メンター役・メンティ役・観察者役に分かれ、ルームを作った人がルームコードを他の2人に伝えます。</p>
        </details>

        <details>
            <summary>練習の内容は他の人に見えますか？</summary>
            <p>ソロ練習とクイズの結果はご本人だけが見られます。トリオ練習は3人で共有する仕組みのため、同じルームに参加した方に対話内容とフィードバックが表示されます。</p>
        </details>

        <details>
            <summary>アカウントを削除したいのですが</summary>
            <p>ログイン後、設定画面のプロフィールからご自身で削除できます。削除すると練習の記録も一緒に消えます。</p>
        </details>

        <details>
            <summary>うまく動かないときは</summary>
            <p><a href="{{ route('contact.create') }}">お問い合わせフォーム</a>からご連絡ください。お使いの端末とブラウザを書き添えていただけると助かります。</p>
        </details>
    </section>

    <section>
        <h2>はじめてみる</h2>
        <p>登録は1分ほどで終わります。確認メールを待つ必要はありません。</p>
        <div class="cta">
            @auth
                <a class="btn btn-primary" href="{{ route('dashboard') }}">ダッシュボードへ</a>
            @else
                <a class="btn btn-primary" href="{{ route('register') }}">新規登録して始める</a>
                <a class="btn btn-ghost" href="{{ route('about') }}">このサイトについて</a>
            @endauth
        </div>
    </section>
@endsection
