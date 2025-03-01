<?php

class TutorAPIConfig {
    const API_BASE_URL = 'https://wedti.com/wp-json';
    const API_KEY = 'administrator...';  // Your WordPress username
    const API_SECRET = '....';  // Your actual WordPress password
    
    // Endpoints
    const ENDPOINT_COURSES = '/tutor/v1/courses';
    const ENDPOINT_TOPICS = '/tutor/v1/topics';
    const ENDPOINT_LESSONS = '/tutor/v1/lessons';
    const ENDPOINT_QUIZ = '/tutor/v1/quizzes';
    
    // Add cookie values from browser
    const COOKIES = [
        'wordpress_logged_in_440a23810da7061fd258594d63874b6a' => 'admin%7C17...',
        'wordpress_sec_440a23810da7061fd258594d63874b6a' => 'admin%7C1...',
        'wp-settings-1' => 'libraryContent%3Dbrowse%26editor%3Dtinymce',
        'wp-settings-time-1' => '1738769303'
    ];
    
    // Add nonce from browser
    const WP_NONCE = '73ee95efa8';
} 
