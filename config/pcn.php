<?php

return [
    'browsershot' => [
        'node_bin' => env('BROWSERSHOT_NODE_BIN', '/usr/bin/node'),
        'npm_bin' => env('BROWSERSHOT_NPM_BIN', '/usr/bin/npm'),
    ],
    'fetcher' => [
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
        'timeout' => 10,
    ],
    'regex' => [
        'price' => '/(\d[\$0-9,]+(\.\d{2})?)/',
        'price_with_spaces' => '/(\d[\$0-9, ]+(\.\d{2})?)/',
    ],
];
