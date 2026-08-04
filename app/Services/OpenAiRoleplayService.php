<?php

namespace App\Services;

use App\Models\Persona;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiRoleplayService
{
    public function reply(Persona $persona, string $scenario, array $history): string
    {
        $apiKey = (string) config('services.openai.key');
        if ($apiKey === '') {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        $transcript = collect($history)
            ->map(fn (array $message) => strtoupper($message['speaker']).': '.$message['content'])
            ->implode("\n");
        $instructions = "あなたはメンタートレーニングの相談者役です。次のペルソナとシナリオを守り、相談者として自然な日本語で2〜4文だけ返答してください。相談者から解決策を先に出さず、メンターの質問に応じて少しずつ本音を話します。\n\nペルソナ: {$persona->name}（{$persona->role}）\n背景: {$persona->background}\n悩み: {$persona->challenge}\n話し方: {$persona->tone}\nシナリオ: {$scenario}";

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(30)
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model', 'gpt-5.6-luna'),
                'instructions' => $instructions,
                'input' => $transcript,
                'store' => false,
                'text' => ['verbosity' => 'low'],
            ])
            ->throw()
            ->json();

        if (is_string(data_get($response, 'output_text')) && trim(data_get($response, 'output_text')) !== '') {
            return trim(data_get($response, 'output_text'));
        }

        foreach (data_get($response, 'output', []) as $item) {
            foreach ($item['content'] ?? [] as $content) {
                if (is_string($content['text'] ?? null) && trim($content['text']) !== '') {
                    return trim($content['text']);
                }
            }
        }

        throw new RuntimeException('OpenAI returned no response text.');
    }
}
