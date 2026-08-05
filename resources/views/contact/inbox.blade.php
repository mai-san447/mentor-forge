@extends('layouts.public')

@section('title', '受信箱 — Mentor Forge')

@section('content')
    <section class="hero">
        <p class="eyebrow">INBOX</p>
        <h1>お問い合わせ受信箱</h1>
        <p class="lead">新しい順に最大100件を表示します。</p>
    </section>

    <section>
        @if ($messages->isEmpty())
            <div class="box"><p>まだお問い合わせはありません。</p></div>
        @else
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>受信日時</th>
                            <th>お名前</th>
                            <th>メールアドレス</th>
                            <th>内容</th>
                            <th>送信元IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr>
                                <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->email }}</td>
                                <td class="body">{{ $message->body }}</td>
                                <td>{{ $message->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
