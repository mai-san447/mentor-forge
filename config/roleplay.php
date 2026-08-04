<?php

return [
    'provider' => env('ROLEPLAY_AI_PROVIDER', 'scripted'),
    'ollama_url' => env('OLLAMA_URL', 'http://host.docker.internal:11434'),
    'ollama_model' => env('OLLAMA_MODEL', 'qwen2.5:7b'),
];
