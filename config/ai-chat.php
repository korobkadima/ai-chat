<?php

return [
    'name' => 'AiChat',

    'models' => [
        'owner'   => App\Models\User::class,
    ],

    'chats' => [
        'identifier' => env('CHAT_IDENTIFIER', 'ulid'),
    ],

    'summarize_chats' => false,

    'default_model' => 'gpt-3.5-turbo',

    'default_prompt' => 'You are an tour guide Matt',

    'callables' => [
        'should_cache' => true,
        'cache_key'    => 'ai-chat-callable-functions',
        'functions' => [
            \App\Callables\CurrentYear::class,
            \App\Callables\WeatherCondition::class,
            \App\Callables\SnowCondition::class,
        ],
    ],
];
