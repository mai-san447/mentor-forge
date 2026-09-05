<x-layouts::app :title="$scenario->title">
    <div class="mx-auto max-w-3xl space-y-6">
        <a href="{{ route('cases.index') }}" class="inline-flex text-sm font-semibold text-teal-700 hover:text-teal-900">← ケース一覧へ</a>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7">
            <p class="text-sm font-semibold text-teal-700">{{ $scenario->persona->role }} / {{ $scenario->difficulty }}</p>
            <h1 class="mt-2 text-2xl font-bold text-zinc-900 sm:text-3xl">{{ $scenario->title }}</h1>
            <div class="mt-5 space-y-4 text-zinc-700">
                <div><h2 class="font-semibold text-zinc-900">状況</h2><p class="mt-1 whitespace-pre-wrap leading-7">{{ $scenario->situation }}</p></div>
                <div><h2 class="font-semibold text-zinc-900">相談者</h2><p class="mt-1 leading-7">{{ $scenario->persona->background }}</p></div>
                <div><h2 class="font-semibold text-zinc-900">今回の目標</h2><p class="mt-1 leading-7">{{ $scenario->goal }}</p></div>
            </div>
        </section>

        @if(!$ownResponse)
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-7">
                <h2 class="text-xl font-bold text-zinc-900">あなたなら、最初にどう返しますか？</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-600">自分の言葉で書いてください。投稿するまで、ほかの人の回答は表示されません。投稿後の編集はできません。</p>

                <form method="POST" action="{{ route('cases.responses.store', $scenario) }}" class="mt-5 space-y-3">
                    @csrf
                    <label for="content" class="sr-only">あなたの回答</label>
                    <textarea id="content" name="content" required maxlength="2000" rows="7" class="block w-full rounded-xl border-amber-300 bg-white text-base leading-7" placeholder="例：そう感じるようになったきっかけを、話せる範囲で教えてもらえますか？">{{ old('content') }}</textarea>
                    @error('content')<p class="text-sm font-semibold text-red-700">{{ $message }}</p>@enderror
                    <button class="w-full rounded-xl bg-teal-700 px-5 py-3 font-semibold text-white hover:bg-teal-800 sm:w-auto">回答を投稿する</button>
                </form>
            </section>

            <section class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-100 p-5 text-center text-zinc-600 sm:p-7">
                <p class="text-2xl" aria-hidden="true">🔒</p>
                <h2 class="mt-2 font-bold text-zinc-800">ほかの人の回答はまだ見られません</h2>
                <p class="mt-1 text-sm">先に自分の回答を投稿すると、匿名の回答一覧が開きます。</p>
            </section>
        @else
            @if(session('status'))
                <div class="rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm font-semibold text-teal-900">{{ session('status') }}</div>
            @endif
            @error('content')<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">{{ $message }}</div>@enderror

            <section class="space-y-4">
                <div>
                    <h2 class="text-2xl font-bold">みんなの回答</h2>
                    <p class="mt-1 text-sm text-zinc-600">名前やメールアドレスは表示しません。正解探しではなく、返し方の違いを比べてみましょう。</p>
                </div>

                @foreach($responses as $index => $response)
                    @php($isOwn = $response->id === $ownResponse->id)
                    <article class="rounded-2xl border p-5 sm:p-6 {{ $isOwn ? 'border-teal-400 bg-teal-50' : 'border-zinc-200 bg-white' }}">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="font-bold {{ $isOwn ? 'text-teal-900' : 'text-zinc-800' }}">{{ $isOwn ? 'あなたの回答' : '匿名の回答 '.($index + 1) }}</h3>
                            @if($isOwn)<span class="rounded-full bg-teal-700 px-3 py-1 text-xs font-semibold text-white">自分</span>@endif
                        </div>
                        <p class="mt-3 whitespace-pre-wrap break-words leading-7 text-zinc-700">{{ $response->content }}</p>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
</x-layouts::app>
