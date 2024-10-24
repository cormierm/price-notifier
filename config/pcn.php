<?php

return [
    'browsershot' => [
        'node_bin' => env('BROWSERSHOT_NODE_BIN', '/usr/bin/node'),
        'npm_bin' => env('BROWSERSHOT_NPM_BIN', '/usr/bin/npm'),
        'chrome_path' => env('BROWSERSHOT_CHROME_PATH', ''),
        'timeout' => env('BROWSERSHOT_TIMEOUT', 60), // in seconds
    ],
    'fetcher' => [
        'user_agent' => env('FETCHER_USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36'),
        'timeout' => 10,
        'delay' => env('FETCHER_DELAY', 30),
        'fetch_null_regions' => env('FETCH_NULL_REGION_WATCHER', false),
        'error_max_length' => env('FETCHER_ERROR_MAX_LENGTH', 2000),
        'puppeteer_host' => env('FETCHER_PUPPETEER_HOST', 'http://localhost:3000'),
    ],
    'regex' => [
        'price' => '/(\d[\$0-9,]*(\.\d{2})?)/',
        'price_with_spaces' => '/(\d[\$0-9, ]+(\.\d{2})?)/',
    ],
    'region' => env('WATCHER_REGION'),
];
