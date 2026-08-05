@extends('layouts.public')

@section('title', 'お問い合わせ — Mentor Forge')
@section('description', 'Mentor Forge へのご質問、不具合のご報告、データの削除依頼はこちらから。')

@section('content')
    <section class="hero">
        <p class="eyebrow">CONTACT</p>
        <h1>お問い合わせ</h1>
        <p class="lead">
            ご質問、不具合のご報告、データの削除依頼などをお送りいただけます。
            返信までお時間をいただく場合があります。
        </p>
    </section>

    <section>
        @if (session('status'))
            <p class="notice">{{ session('status') }}</p>
        @endif

        <div class="box">
            <form method="POST" action="{{ route('contact.store') }}">
                @csrf

                {{-- ボット対策。人には見えないので、入力があれば自動送信と判断する。 --}}
                <div class="hp" aria-hidden="true">
                    <label for="website">ここは入力しないでください</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="field">
                    <label for="name">お名前</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           maxlength="50" required autocomplete="name">
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="email">メールアドレス
                        <span class="hint">— 返信先としてのみ使用します</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           maxlength="255" required autocomplete="email">
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="field">
                    <label for="body">お問い合わせ内容
                        <span class="hint">— 不具合のご報告は、お使いの端末とブラウザも書き添えてください</span>
                    </label>
                    <textarea id="body" name="body" maxlength="1000" required>{{ old('body') }}</textarea>
                    @error('body') <p class="error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn btn-primary">送信する</button>
            </form>
        </div>

        <p style="font-size:14px;color:#71717a;margin-top:16px;">
            送信いただいた内容は、返信と不具合の調査にのみ使用します。詳しくは
            <a href="{{ route('about') }}">このサイトについて</a> をご覧ください。
        </p>
    </section>
@endsection
