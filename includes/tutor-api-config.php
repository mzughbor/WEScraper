<?php

class TutorAPIConfig {
    const API_BASE_URL = 'https://wedti.com/wp-json';
    const API_KEY = 'admin';  // Your WordPress username
    const API_SECRET = 'Mostafa3010***###';  // Your actual WordPress password
    
    // Endpoints
    const ENDPOINT_COURSES = '/tutor/v1/courses';
    const ENDPOINT_TOPICS = '/tutor/v1/topics';
    const ENDPOINT_LESSONS = '/tutor/v1/lessons';
    const ENDPOINT_QUIZ = '/tutor/v1/quizzes';
    
    // Add cookie values from browser
    const COOKIES = [
        'wordpress_logged_in_440a23810da7061fd258594d63874b6a' => 'admin%7C1739620196%7CMLwPmDwRckMgditonMvwcVA1SQLYJZItCf6YSW9kXpY%7Cf6df8490f8151eab8deeb870ea39f3570faf2932713e0fa1a59fdf06a42f0cc0',
        'wordpress_sec_440a23810da7061fd258594d63874b6a' => 'admin%7C1739620196%7CMLwPmDwRckMgditonMvwcVA1SQLYJZItCf6YSW9kXpY%7C595f3cbe22b77b07ee7f573aea9e4e4b3e1fae0cac3d34e9e02423e0b5b9c640',
        'wp-settings-1' => 'libraryContent%3Dbrowse%26editor%3Dtinymce',
        'wp-settings-time-1' => '1738769303'
    ];
    
    // Add nonce from browser
    const WP_NONCE = '73ee95efa8';
} 