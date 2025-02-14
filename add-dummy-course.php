<?php
require_once __DIR__ . '/includes/tutor-course-manager.php';

try {
    // Initialize the API client and course manager
    $api_client = new TutorAPIClient();
    $course_manager = new TutorCourseManager($api_client);
    
    // Dummy course data
    $course_title = "Python Programming Basics";
    $course_description = "Learn Python programming from scratch. This course covers fundamental concepts of Python programming language.";
    $course_benefits = [
        "Understand Python syntax and basic programming concepts",
        "Build simple Python applications",
        "Learn object-oriented programming in Python"
    ];
    $course_requirements = [
        "Basic computer knowledge",
        "No prior programming experience needed",
        "A computer with internet access"
    ];
    
    echo "Creating course...\n";
    
    // Create the course
    $course = $course_manager->createCourse(
        $course_title,
        $course_description,
        $course_benefits,
        $course_requirements
    );
    
    echo "Course created with ID: " . $course['ID'] . "\n";
    
    // Add topics and lessons
    $topics = [
        [
            'title' => 'Getting Started with Python',
            'summary' => 'Introduction to Python programming',
            'lessons' => [
                [
                    'title' => 'What is Python?',
                    'content' => 'Python is a high-level, interpreted programming language...'
                ],
                [
                    'title' => 'Installing Python',
                    'content' => 'Step by step guide to install Python on your computer...'
                ]
            ]
        ],
        [
            'title' => 'Python Basics',
            'summary' => 'Learn fundamental Python concepts',
            'lessons' => [
                [
                    'title' => 'Variables and Data Types',
                    'content' => 'Learn about different data types in Python...'
                ],
                [
                    'title' => 'Control Structures',
                    'content' => 'Understanding if statements and loops in Python...'
                ]
            ]
        ]
    ];
    
    // Add each topic and its lessons
    foreach ($topics as $topic_data) {
        echo "\nCreating topic: {$topic_data['title']}\n";
        
        $topic = $course_manager->addTopic(
            $course['ID'],
            $topic_data['title'],
            $topic_data['summary']
        );
        
        echo "Topic created with ID: " . $topic['topic_id'] . "\n";
        
        // Add lessons for this topic
        foreach ($topic_data['lessons'] as $lesson_data) {
            echo "Adding lesson: {$lesson_data['title']}\n";
            
            $lesson = $course_manager->addLesson(
                $topic['topic_id'],
                $lesson_data['title'],
                $lesson_data['content']
            );
            
            echo "Lesson created with ID: " . $lesson['lesson_id'] . "\n";
        }
    }
    
    echo "\nDummy course creation completed successfully!\n";
    echo "You can view the course in WordPress admin panel.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Remove the getResponse() call and use the API client's last response
    if ($api_client && method_exists($api_client, 'getLastResponse')) {
        $response = $api_client->getLastResponse();
        if ($response) {
            echo "Last API Response: " . $response . "\n";
        }
    }
    
    // Add debug information
    echo "Debug Information:\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "cURL Version: " . curl_version()['version'] . "\n";
} 