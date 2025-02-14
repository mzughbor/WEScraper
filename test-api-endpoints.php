<?php
require_once __DIR__ . '/includes/tutor-api-client.php';

function testEndpoint($api_client, $endpoint, $method = 'GET', $data = null) {
    echo "\nTesting endpoint: " . $endpoint . " [" . $method . "]\n";
    echo "----------------------------------------\n";
    
    try {
        $response = $api_client->makeRequest($endpoint, $method, $data);
        echo "Success!\n";
        return true;
    } catch (Exception $e) {
        echo "Failed: " . $e->getMessage() . "\n";
        return false;
    }
}

try {
    $api_client = new TutorAPIClient();
    
    // Test different authentication methods
    echo "Testing with Basic Auth...\n";
    
    // 1. Test basic endpoints
    $endpoints = [
        '/courses' => 'GET',
        '/topics' => 'GET',
        '/lessons' => 'GET',
        '/instructors' => 'GET'
    ];
    
    foreach ($endpoints as $endpoint => $method) {
        testEndpoint($api_client, $endpoint, $method);
    }
    
    // 2. Test WordPress REST API root
    echo "\nTesting WordPress REST API root...\n";
    testEndpoint($api_client, '', 'GET');
    
    // 3. Test Tutor LMS specific endpoints
    echo "\nTesting Tutor LMS specific endpoints...\n";
    $tutor_endpoints = [
        '/tutor/v1/courses' => 'GET',
        '/wp/v2/courses' => 'GET',
        '/wp/v2/tutor-courses' => 'GET'
    ];
    
    foreach ($tutor_endpoints as $endpoint => $method) {
        testEndpoint($api_client, $endpoint, $method);
    }
    
    // 4. Test with different auth headers
    echo "\nTesting different auth methods...\n";
    
    // Test JWT auth if available
    $jwt_client = new TutorAPIClient();
    $jwt_auth_data = [
        'username' => TutorAPIConfig::API_KEY,
        'password' => TutorAPIConfig::API_SECRET
    ];
    
    testEndpoint($jwt_client, '/jwt-auth/v1/token', 'POST', $jwt_auth_data);
    
    // 5. Print API documentation if available
    echo "\nTrying to fetch API documentation...\n";
    testEndpoint($api_client, '/tutor/v1', 'GET');
    
} catch (Exception $e) {
    echo "Test script error: " . $e->getMessage() . "\n";
}

// Add verbose output for debugging
echo "\nDebug Information:\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "cURL Version: " . curl_version()['version'] . "\n";
echo "SSL Version: " . curl_version()['ssl_version'] . "\n";

// Test SSL connection
echo "\nTesting SSL connection to wedti.com...\n";
$ch = curl_init('https://wedti.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo "SSL Error: " . curl_error($ch) . "\n";
} else {
    echo "SSL connection successful\n";
}
curl_close($ch); 