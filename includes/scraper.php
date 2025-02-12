<?php
/**
 * Course Scraper Class
 * 
 * Handles scraping course details from Mindluster with human-like behavior
 */

class MindLusterScraper {
    private $cookie_file;
    private $session_cookies = [
        'XSRF-TOKEN' => '',
        'laravel_session' => '',
        'FCNEC' => ''
    ];

    public function __construct($xsrf_token, $laravel_session, $fcnec = '') {
        $this->session_cookies = [
            'XSRF-TOKEN' => urldecode($xsrf_token),
            'laravel_session' => $laravel_session,
            'FCNEC' => urldecode($fcnec)
        ];
        
        // Create cookie file
        $this->cookie_file = dirname(__FILE__) . '/cookies.txt';
        $cookie_content = "www.mindluster.com\tTRUE\t/\tTRUE\t1739428800\tXSRF-TOKEN\t" . $this->session_cookies['XSRF-TOKEN'] . "\n";
        $cookie_content .= "www.mindluster.com\tTRUE\t/\tFALSE\t1739428800\tlaravel_session\t" . $this->session_cookies['laravel_session'] . "\n";
        if (!empty($fcnec)) {
            $cookie_content .= "www.mindluster.com\tTRUE\t/\tFALSE\t1739428800\tFCNEC\t" . $this->session_cookies['FCNEC'] . "\n";
        }
        file_put_contents($this->cookie_file, $cookie_content);
    }

    /**
     * Initialize curl with common options
     */
    private function initCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
        
        // Set headers including CSRF token from cookie
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9',
            'Connection: keep-alive',
            'X-XSRF-TOKEN: ' . $this->session_cookies['XSRF-TOKEN'],
            'Origin: https://www.mindluster.com',
            'Referer: https://www.mindluster.com/'
        ]);

        return $ch;
    }

    /**
     * Verify if we're logged in by checking a protected page
     */
    private function verifyLogin() {
        $ch = $this->initCurl('https://www.mindluster.com/updatepicture');
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Save response for debugging
        file_put_contents('login_check.html', $response);
        
        // Check if we're redirected to login page or if page contains login form
        $is_logged_in = (
            $http_code === 200 && 
            stripos($response, 'Update Profile Picture') !== false &&
            stripos($response, 'Login') === false
        );

        curl_close($ch);
        
        if (!$is_logged_in) {
            throw new Exception('Not logged in. Session cookies might be expired or invalid.');
        }

        return true;
    }

    /**
     * Scrape course details
     */
    public function scrapeCourse($course_url) {
        // First verify we're logged in
        echo "Verifying login status...\n";
        $this->verifyLogin();
        echo "Login verified successfully!\n";

        $ch = $this->initCurl($course_url);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 401 || $http_code === 403) {
            throw new Exception('Authentication failed. Session might have expired.');
        }

        // Save response for debugging
        file_put_contents('course_response.html', $response);
        
        // Check if we got redirected to login page
        if (stripos($response, 'Please verify that you are not a robot') !== false ||
            stripos($response, 'Login With Facebook') !== false) {
            throw new Exception('Got redirected to login page. Session might have expired.');
        }

        echo "Debug: Course page response size: " . strlen($response) . " bytes\n";
        
        $dom = new DOMDocument();
        @$dom->loadHTML($response);
        $xpath = new DOMXPath($dom);

        $course_data = [
            'title' => '',
            'instructor' => '',
            'lessons' => [],
            'reviews' => [],
            'rating' => '',
            'description' => ''
        ];

        // Extract title - updated selector
        $course_data['title'] = $this->extractContent($xpath, "//div[@class='course_preview_slider']//h1[@id='course_title']");
        
        // Extract instructor - updated selector
        $instructor_node = $xpath->query("//div[contains(@class, 'cat')]/span/h2/b");
        if ($instructor_node->length > 0) {
            $course_data['instructor'] = trim($instructor_node->item(0)->textContent);
        }
        
        // Extract rating - updated selector
        $rating_nodes = $xpath->query("//div[contains(@class, 'ratings_stars')]//div[contains(@class, 'full-stars')]");
        if ($rating_nodes->length > 0) {
            $style = $rating_nodes->item(0)->getAttribute('style');
            if (preg_match('/width:(\d+)%/', $style, $matches)) {
                $course_data['rating'] = $matches[1] / 20; // Convert percentage to 5-star scale
            }
        }

        // Extract description - updated selector
        $desc_node = $xpath->query("//div[contains(@class, 'm3aarf_card')][last()]");
        if ($desc_node->length > 0) {
            $course_data['description'] = trim($desc_node->item(0)->textContent);
        }

        // Extract lessons - updated selector
        $lessons = $xpath->query("//div[@class='lesson_list']//div[contains(@class, 'lesson_thumb')]");
        foreach ($lessons as $lesson) {
            $title = $this->extractContent($xpath, ".//h3", $lesson);
            $duration = $this->extractContent($xpath, ".//span[@class='lesson_duration']", $lesson);
            
            if (!empty($title)) {
                $course_data['lessons'][] = [
                    'title' => $title,
                    'duration' => $duration
                ];
            }
        }

        // Extract reviews - updated selector
        $reviews = $xpath->query("//div[contains(@class, 'review-block')]");
        foreach ($reviews as $review) {
            $author = $this->extractContent($xpath, ".//div[contains(@class, 'user_name')]", $review);
            $content = $this->extractContent($xpath, ".//p[@id='review_content']", $review);
            $date = $this->extractContent($xpath, ".//div[contains(@class, 'review_date')]", $review);
            
            if (!empty($author) || !empty($content)) {
                $course_data['reviews'][] = [
                    'author' => $author,
                    'content' => $content,
                    'date' => $date
                ];
            }
        }

        // Add debug output
        echo "\nDebug Information:\n";
        echo "Title found: " . (!empty($course_data['title']) ? "Yes" : "No") . "\n";
        echo "Instructor found: " . (!empty($course_data['instructor']) ? "Yes" : "No") . "\n";
        echo "Lessons found: " . count($course_data['lessons']) . "\n";
        echo "Reviews found: " . count($course_data['reviews']) . "\n";
        echo "Rating found: " . (!empty($course_data['rating']) ? "Yes" : "No") . "\n";
        echo "Description found: " . (!empty($course_data['description']) ? "Yes" : "No") . "\n";

        if (empty($course_data['title']) && empty($course_data['description'])) {
            echo "\nWarning: Could not extract course data. Check course_response.html for the actual page content.\n";
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
    $scraper = new MindLusterScraper('your_xsrf_token', 'your_laravel_session', 'your_fcnec');
    
    $course_data = $scraper->scrapeCourse('https://www.mindluster.com/certificate/16207/Blockchain-development-video');
    echo json_encode($course_data, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 