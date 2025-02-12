#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/scraper.php';

// Parse command line arguments
$options = getopt('', ['url:', 'xsrf-token:', 'laravel-session:', 'fcnec::']);

$url = $options['url'] ?? null;
$xsrf_token = $options['xsrf-token'] ?? null;
$laravel_session = $options['laravel-session'] ?? null;
$fcnec = $options['fcnec'] ?? '';

if (!$url || !$xsrf_token || !$laravel_session) {
    echo "Usage: php scrape.php --url=<course_url> --xsrf-token=<token> --laravel-session=<session> [--fcnec=<fcnec>]\n";
    exit(1);
}

try {
    echo "Starting scraper...\n";
    
    $scraper = new MindLusterScraper($xsrf_token, $laravel_session, $fcnec);
    
    echo "Scraping course data...\n";
    $course_data = $scraper->scrapeCourse($url);
    
    // Save to JSON file
    $output_file = 'course_data_' . date('Y-m-d_H-i-s') . '.json';
    file_put_contents($output_file, json_encode($course_data, JSON_PRETTY_PRINT));
    
    echo "\nScraping completed successfully!\n";
    echo "Data saved to: $output_file\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}