<?php
require_once __DIR__ . '/includes/tutor-course-manager.php';

try {
    // Initialize the API client
    $api_client = new TutorAPIClient();
    $course_manager = new TutorCourseManager($api_client);
    
    // Create a test course
    $course = $course_manager->createCourse(
        'Test Course via API',
        'This is a test course created via the Tutor LMS API.',
        ['Benefit 1', 'Benefit 2'],
        ['Requirement 1', 'Requirement 2']
    );
    
    echo "Course created with ID: " . $course['ID'] . "\n";
    
    // Add a topic
    $topic = $course_manager->addTopic(
        $course['ID'],
        'Introduction',
        'Getting started with the course'
    );
    
    echo "Topic created with ID: " . $topic['topic_id'] . "\n";
    
    // Add a lesson
    $lesson = $course_manager->addLesson(
        $topic['topic_id'],
        'Welcome to the Course',
        'This is the first lesson content.'
    );
    
    echo "Lesson created with ID: " . $lesson['lesson_id'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 