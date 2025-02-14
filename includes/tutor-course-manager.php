<?php
require_once __DIR__ . '/tutor-api-client.php';

class TutorCourseManager {
    private $api_client;
    
    public function __construct(TutorAPIClient $api_client) {
        $this->api_client = $api_client;
    }
    
    public function createCourse($title, $description, $benefits = [], $requirements = []) {
        $course_data = [
            'post_author' => 1, // Admin user ID
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'publish',
            'additional_content' => [
                'course_benefits' => implode("\n", $benefits),
                'course_requirements' => implode("\n", $requirements),
                'course_duration' => [
                    'hours' => 1,
                    'minutes' => 30
                ]
            ],
            'course_level' => 'beginner'
        ];
        
        return $this->api_client->makeRequest(TutorAPIConfig::ENDPOINT_COURSES, 'POST', $course_data);
    }
    
    public function addTopic($course_id, $title, $summary = '') {
        $topic_data = [
            'topic_course_id' => $course_id,
            'topic_title' => $title,
            'topic_summary' => $summary,
            'topic_author' => 1
        ];
        
        return $this->api_client->makeRequest(TutorAPIConfig::ENDPOINT_TOPICS, 'POST', $topic_data);
    }
    
    public function addLesson($topic_id, $title, $content) {
        $lesson_data = [
            'topic_id' => $topic_id,
            'lesson_title' => $title,
            'lesson_content' => $content,
            'lesson_author' => 1
        ];
        
        return $this->api_client->makeRequest(TutorAPIConfig::ENDPOINT_LESSONS, 'POST', $lesson_data);
    }
} 