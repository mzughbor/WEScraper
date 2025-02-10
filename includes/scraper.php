<?php
/**
 * Course Scraper Class
 * 
 * Handles scraping course details from Mindluster with human-like behavior
 */

class MindLusterScraper {
    private $cookie_file;
    private $user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0'
    ];
    
    private $credentials = [
        'email' => '',
        'password' => ''
    ];

    public function __construct($email, $password) {
        $this->credentials['email'] = $email;
        $this->credentials['password'] = $password;
        $this->cookie_file = dirname(__FILE__) . '/cookies.txt';
    }

    /**
     * Initialize curl with common options
     */
    private function initCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agents[array_rand($this->user_agents)]);
        
        // Common headers to mimic browser behavior
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1'
        ]);

        return $ch;
    }

    /**
     * Login to Mindluster
     */
    public function login() {
        $ch = $this->initCurl('https://www.mindluster.com/login');
        
        // First get the login page to obtain any CSRF token
        $login_page = curl_exec($ch);
        
        // Extract CSRF token if needed
        preg_match('/<input type="hidden" name="_token" value="(.*?)">/i', $login_page, $matches);
        $csrf_token = isset($matches[1]) ? $matches[1] : '';

        // Prepare login data
        $login_data = http_build_query([
            '_token' => $csrf_token,
            'email' => $this->credentials['email'],
            'password' => $this->credentials['password']
        ]);

        // Set POST options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);
        
        // Add additional headers for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/x-www-form-urlencoded',
            'Origin: https://www.mindluster.com',
            'Referer: https://www.mindluster.com/login'
        ], curl_getinfo($ch, CURLINFO_HTTPHEADER)));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);

        return $http_code === 200;
    }

    /**
     * Scrape course details
     */
    public function scrapeCourse($course_url) {
        // Add random delay to mimic human behavior
        usleep(rand(500000, 2000000)); // 0.5 to 2 seconds

        $ch = $this->initCurl($course_url);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        // Parse the response using DOMDocument
        $dom = new DOMDocument();
        @$dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        // Extract course details
        $course_data = [
            'title' => $this->extractContent($xpath, "//h1[contains(@class, 'course-title')]"),
            'instructor' => $this->extractContent($xpath, "//div[contains(@class, 'instructor-name')]"),
            'lessons' => [],
            'reviews' => [],
            'rating' => $this->extractContent($xpath, "//div[contains(@class, 'course-rating')]"),
            'description' => $this->extractContent($xpath, "//div[contains(@class, 'course-description')]")
        ];

        // Extract lessons
        $lessons = $xpath->query("//div[contains(@class, 'lesson-item')]");
        foreach ($lessons as $lesson) {
            $course_data['lessons'][] = [
                'title' => $this->extractContent($xpath, ".//div[contains(@class, 'lesson-title')]", $lesson),
                'duration' => $this->extractContent($xpath, ".//div[contains(@class, 'lesson-duration')]", $lesson)
            ];
        }

        // Extract reviews
        $reviews = $xpath->query("//div[contains(@class, 'review-item')]");
        foreach ($reviews as $review) {
            $course_data['reviews'][] = [
                'author' => $this->extractContent($xpath, ".//div[contains(@class, 'review-author')]", $review),
                'rating' => $this->extractContent($xpath, ".//div[contains(@class, 'review-rating')]", $review),
                'content' => $this->extractContent($xpath, ".//div[contains(@class, 'review-content')]", $review)
            ];
        }

        return $course_data;
    }

    /**
     * Helper function to extract content using XPath
     */
    private function extractContent($xpath, $query, $context = null) {
        $nodes = $context ? 
            $xpath->query($query, $context) : 
            $xpath->query($query);
            
        if ($nodes->length > 0) {
            return trim($nodes->item(0)->textContent);
        }
        return '';
    }
}

// Usage example:
try {
    $scraper = new MindLusterScraper('your_email@example.com', 'your_password');
    
    if ($scraper->login()) {
        $course_data = $scraper->scrapeCourse('https://www.mindluster.com/certificate/16207/Blockchain-development-video');
        echo json_encode($course_data, JSON_PRETTY_PRINT);
    } else {
        echo "Login failed!";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 