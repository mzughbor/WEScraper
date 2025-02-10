<?php
/**
 * Scraper Configuration
 */

return [
    'credentials' => [
        'email' => 'your_email@example.com',
        'password' => 'your_password'
    ],
    
    'delays' => [
        'min' => 500000, // 0.5 seconds
        'max' => 2000000 // 2 seconds
    ],
    
    'retry' => [
        'max_attempts' => 3,
        'delay_between_attempts' => 5 // seconds
    ],
    
    'user_agents' => [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0'
    ]
]; 