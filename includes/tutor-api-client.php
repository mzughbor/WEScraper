<?php
require_once __DIR__ . '/tutor-api-config.php';

class TutorAPIClient {
    private $api_key;
    private $api_secret;
    private $last_response;
    private $cookie_jar;
    private $nonce;
    
    public function __construct($api_key = null, $api_secret = null) {
        $this->api_key = $api_key ?? TutorAPIConfig::API_KEY;
        $this->api_secret = $api_secret ?? TutorAPIConfig::API_SECRET;
        $this->cookie_jar = tempnam(sys_get_temp_dir(), 'cookie_');
        
        // Instead of logging in, we'll use the cookie directly
        $this->createCookieFile();
    }
    
    private function createCookieFile() {
        // Replace these values with what you copied from browser
        $cookies = [
            'wordpress_logged_in_440a23810da7061fd258594d63874b6a' => 'admin%7C1...', // Copy from browser
            'wordpress_sec_440a23810da7061fd258594d63874b6a' => 'admin%7C1...',       // Copy from browser
            'wp-settings-1' => 'libraryContent%3Dbrowse%26editor%3Dtinymce...',       // Copy from browser
            'wp-settings-time-1' => '1738769303'   // Copy from browser
        ];
        
        $cookie_content = '';
        foreach ($cookies as $name => $value) {
            $cookie_content .= "wedti.com\tTRUE\t/\tTRUE\t0\t$name\t$value\n";
        }
        
        file_put_contents($this->cookie_jar, $cookie_content);
        echo "Cookie file created\n";
    }
    
    public function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = TutorAPIConfig::API_BASE_URL . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_jar);
        
        // Use the nonce from browser
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-WP-Nonce: YOUR_NONCE_FROM_BROWSER', // Replace with actual nonce
            'Referer: https://wedti.com/wp-admin/',
            'Origin: https://wedti.com'
        ];
        
        echo "Making request to: " . $url . "\n";
        echo "Method: " . $method . "\n";
        echo "Headers: " . print_r($headers, true) . "\n";
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                echo "Data: " . print_r($data, true) . "\n";
            }
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        $this->last_response = $response;
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Curl error: $error\nURL: $url\nMethod: $method");
        }
        
        curl_close($ch);
        
        echo "API Response Code: " . $http_code . "\n";
        echo "API Response: " . $response . "\n";
        
        if ($http_code !== 200 && $http_code !== 201) {
            throw new Exception('API request failed with status code: ' . $http_code . ' Response: ' . $response);
        }
        
        return json_decode($response, true);
    }
    
    public function getLastResponse() {
        return $this->last_response;
    }
    
    public function __destruct() {
        if (file_exists($this->cookie_jar)) {
            unlink($this->cookie_jar);
        }
    }
}
