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
            @error('reflection')<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">{{ $message }}</div>@enderror

            <section class="space-y-4">
                <div>
                    <h2 class="text-2xl font-bold">あなたの最初の回答</h2>
                    <p class="mt-1 text-sm text-zinc-600">ほかの回答を見る前に書いた、自分の出発点です。</p>
                </div>
                <article class="rounded-2xl border border-teal-400 bg-teal-50 p-5 sm:p-6">
                    <div class="flex items-center justify-between gap-3"><h3 class="font-bold text-teal-900">あなたの回答</h3><span class="rounded-full bg-teal-700 px-3 py-1 text-xs font-semibold text-white">自分</span></div>
                    <p class="mt-3 whitespace-pre-wrap break-words leading-7 text-zinc-700">{{ $ownResponse->content }}</p>
                </article>
            </section>

            <section class="space-y-4">
                <div>
                    <h2 class="text-2xl font-bold">6つまでの異なる返し方</h2>
                    <p class="mt-1 text-sm text-zinc-600">実際にほかの利用者が書いた回答を、匿名・ランダム順で表示しています。点数や人気順ではありません。</p>
                </div>

                @forelse($otherResponses as $index => $response)
                    <article class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6">
                        <h3 class="font-bold text-zinc-800">匿名の回答 {{ chr(65 + $index) }}</h3>
                        <p class="mt-3 whitespace-pre-wrap break-words leading-7 text-zinc-700">{{ $response->content }}</p>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-100 p-6 text-center text-zinc-600">比較できる回答がまだありません。ほかの利用者の回答が集まると、ここに匿名で表示されます。</div>
                @endforelse
            </section>

            @if($reflection)
                <section class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 sm:p-7">
                    <p class="text-sm font-semibold text-indigo-700">保存済みの振り返り</p>
                    <h2 class="mt-1 text-2xl font-bold text-zinc-900">違いを、次の実践へ</h2>
                    <div class="mt-5 space-y-5">
                        <div><h3 class="font-semibold text-zinc-900">選んだ匿名回答</h3><p class="mt-1 whitespace-pre-wrap leading-7 text-zinc-700">{{ $reflection->selected_response_content }}</p></div>
                        <div><h3 class="font-semibold text-zinc-900">選んだ理由</h3><p class="mt-1 whitespace-pre-wrap leading-7 text-zinc-700">{{ $reflection->selection_reason }}</p></div>
                        <div><h3 class="font-semibold text-zinc-900">自分の回答との違い</h3><p class="mt-1 whitespace-pre-wrap leading-7 text-zinc-700">{{ $reflection->difference }}</p></div>
                        <div class="rounded-xl bg-white p-4"><h3 class="font-semibold text-indigo-900">次に試す行動</h3><p class="mt-1 whitespace-pre-wrap leading-7 text-zinc-800">{{ $reflection->next_action }}</p></div>
                    </div>
                </section>
            @elseif($otherResponses->isNotEmpty())
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-7">
                    <h2 class="text-2xl font-bold text-zinc-900">気になる回答を1つ選ぶ</h2>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">模範回答を選ぶ場ではありません。「自分にはなかった」「次に試してみたい」と感じる返し方を選び、違いを言葉にしてみましょう。</p>

                    <form method="POST" action="{{ route('cases.reflection.store', $scenario) }}" class="mt-5 space-y-5">
                        @csrf
                        <fieldset>
                            <legend class="font-semibold text-zinc-900">選ぶ回答</legend>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                @foreach($otherResponses as $index => $response)
                                    <label class="flex cursor-pointer gap-3 rounded-xl border border-amber-300 bg-white p-4 hover:border-teal-500">
                                        <input type="radio" name="selected_response_id" value="{{ $response->id }}" required @checked((string) old('selected_response_id') === (string) $response->id) class="mt-1 text-teal-700">
                                        <span><span class="font-semibold">匿名の回答 {{ chr(65 + $index) }}</span><span class="mt-1 line-clamp-3 block text-sm leading-6 text-zinc-600">{{ $response->content }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selected_response_id')<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror
                        </fieldset>

                        <div><label for="selection_reason" class="font-semibold text-zinc-900">なぜ気になりましたか？</label><textarea id="selection_reason" name="selection_reason" required maxlength="2000" rows="3" class="mt-2 block w-full rounded-xl border-amber-300 bg-white" placeholder="自分にはなかった視点、試してみたい点など">{{ old('selection_reason') }}</textarea>@error('selection_reason')<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror</div>
                        <div><label for="difference" class="font-semibold text-zinc-900">自分の回答と、何が違いますか？</label><textarea id="difference" name="difference" required maxlength="2000" rows="3" class="mt-2 block w-full rounded-xl border-amber-300 bg-white" placeholder="言葉の選び方、問いかける順番、前提の置き方など">{{ old('difference') }}</textarea>@error('difference')<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror</div>
                        <div><label for="next_action" class="font-semibold text-zinc-900">次の面談で、何を試しますか？</label><textarea id="next_action" name="next_action" required maxlength="1000" rows="3" class="mt-2 block w-full rounded-xl border-amber-300 bg-white" placeholder="例：助言する前に、相手が今どう感じているかを1回たずねる">{{ old('next_action') }}</textarea>@error('next_action')<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror</div>
                        <button class="w-full rounded-xl bg-indigo-700 px-5 py-3 font-semibold text-white hover:bg-indigo-800 sm:w-auto">振り返りを保存する</button>
                    </form>
                </section>
            @endif
        @endif
    </div>
</x-layouts::app>
