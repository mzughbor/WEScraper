<?php
require_once __DIR__ . '/includes/tutor-api-client.php';

try {
    $api_client = new TutorAPIClient();
    
    // Try a simple GET request first
    echo "Testing API connection...\n";
    $response = $api_client->makeRequest('/courses', 'GET');
    
    echo "Connection successful!\n";
    echo "Response: " . print_r($response, true) . "\n";
    
} catch (Exception $e) {
    echo "Connection test failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
} 