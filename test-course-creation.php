<?php
require_once __DIR__ . '/includes/tutor-api-client.php';

try {
    echo "Initializing API client...\n";
    $api_client = new TutorAPIClient();
    
    // First test if we can get courses
    echo "\nTesting GET courses...\n";
    $courses = $api_client->makeRequest('/tutor/v1/courses', 'GET');
    
    // Then try to create a course
    echo "\nTesting POST course...\n";
    $course_data = [
        'post_title' => 'Test Course',
        'post_content' => 'This is a test course',
        'post_status' => 'draft',  // Start as draft
        'post_type' => 'courses'
    ];
    
    $new_course = $api_client->makeRequest('/tutor/v1/courses', 'POST', $course_data);
    echo "Course created successfully!\n";
    print_r($new_course);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (isset($api_client)) {
        echo "Last Response: " . $api_client->getLastResponse() . "\n";
    }
    
    echo "\nDebug Information:\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "cURL Version: " . curl_version()['version'] . "\n";
} 