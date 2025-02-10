#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/scraper.php';  // Temporary direct include


// Load configuration
$config = require_once __DIR__ . '/includes/scraper-config.php';

// Parse command line arguments
$options = getopt('', ['url:', 'email:', 'password:']);

$url = $options['url'] ?? null;
$email = $options['email'] ?? $config['credentials']['email'];
$password = $options['password'] ?? $config['credentials']['password'];

if (!$url) {
    echo "Usage: php scrape.php --url=<course_url> [--email=<email> --password=<password>]\n";
    exit(1);
}

try {
    echo "Starting scraper...\n";
    
    $scraper = new MindLusterScraper($email, $password);
    
    echo "Logging in...\n";
    if ($scraper->login()) {
        echo "Login successful!\n";
        echo "Scraping course data...\n";
        
        $course_data = $scraper->scrapeCourse($url);
        
        // Save to JSON file
        $output_file = 'course_data_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($output_file, json_encode($course_data, JSON_PRETTY_PRINT));
        
        echo "\nScraping completed successfully!\n";
        echo "Data saved to: $output_file\n";
    } else {
        echo "Login failed! Please check your credentials.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1); 
}
