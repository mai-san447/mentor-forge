<?php

namespace App\Services;

use App\Models\Persona;
use Illuminate\Support\Facades\Http;

class RoleplayReplyService
{
    /** @param array<int, array{speaker: string, content: string}> $history */
    public function reply(Persona $persona, string $scenario, array $history): string
    {
        return match (config('roleplay.provider')) {
            'ollama' => $this->ollama($persona, $scenario, $history),
            'openai' => app(OpenAiRoleplayService::class)->reply($persona, $scenario, $history),
            default => $this->scripted($persona, $history),
        };
    }

    /** @param array<int, array{speaker: string, content: string}> $history */
    private function ollama(Persona $persona, string $scenario, array $history): string
    {
        $transcript = collect($history)->map(fn (array $message) => $message['speaker'].': '.$message['content'])->implode("\n");
        $response = Http::timeout(30)->post(config('roleplay.ollama_url').'/api/chat', [
            'model' => config('roleplay.ollama_model'),
            'stream' => false,
            'messages' => [['role' => 'system', 'content' => "あなたは{$persona->name}という相談者です。{$persona->background} 悩みは{$persona->challenge}。シナリオは{$scenario}。相談者として自然な日本語で短く返答してください。"], ['role' => 'user', 'content' => $transcript]],
        ])->throw()->json();

        return (string) data_get($response, 'message.content', 'うまく言葉にできないのですが、もう少し話を聞いてもらえますか？');
    }

    /** @param array<int, array{speaker: string, content: string}> $history */
    private function scripted(Persona $persona, array $history): string
    {
        $lastMessage = collect($history)->where('speaker', 'mentor')->last();
        $message = is_array($lastMessage) ? (string) ($lastMessage['content'] ?? '') : '';

        if (preg_match('/どんな|どう|なぜ|何が|教えて/u', $message)) {
            return 'そうですね…。'.$persona->challenge.'ことが一番気になっています。';
        }

        if (preg_match('/つら|大変|不安|心配|感じ/u', $message)) {
            return 'そう言ってもらえると少しほっとします。実はずっと不安でした。';
        }

        if (preg_match('/べき|しなさい|すぐに|絶対/u', $message)) {
            return '正しいのかもしれませんが、今の自分にできるか少し不安です。';
        }

        return 'ありがとうございます。まだうまく言葉にできないのですが、もう少し話を聞いてもらえますか？';
    }
}
