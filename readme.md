# MindLuster Course Scraper

A PHP-based web scraper for extracting course information from MindLuster.com. This tool allows you to download course details including title, instructor, lessons, reviews, ratings, and descriptions.

## Prerequisites

- PHP 7.4 or higher
- Composer
- cURL extension for PHP
- Valid MindLuster.com account cookies

## Installation

1. Clone this repository:
bash
git clone https://github.com/yourusername/course-scraper.git
cd course-scraper


2. Install dependencies using Composer:
bash
composer install


## Usage

### Getting Required Cookies

Before using the scraper, you need to get your session cookies from MindLuster.com:

1. Log in to MindLuster.com using your browser
2. Open Developer Tools (F12 in most browsers)
3. Go to the "Application" or "Storage" tab
4. Look for Cookies under "www.mindluster.com"
5. Copy the values for:
   - XSRF-TOKEN
   - laravel_session
   - FCNEC (optional)

### Running the Scraper

Use the command line script with your cookies:
bash
php scrape.php \
--url="https://www.mindluster.com/certificate/[COURSE_ID]/[COURSE_NAME]" \
--xsrf-token="your_xsrf_token_here" \
--laravel-session="your_laravel_session_here" \
--fcnec="your_fcnec_here"

Example:
bash
php scrape.php \
--url="https://www.mindluster.com/certificate/16207/Blockchain-development-video" \
--xsrf-token="eyJpdiI6IktqY0d1..." \
--laravel-session="MIKtuuCRQNHiOqt..." \
--fcnec="%5B%5B%22AKsRol9p..."

### Output

The scraper will:
1. Verify login status
2. Scrape the course page
3. Save the results to a JSON file named `course_data_[TIMESTAMP].json`

Example output file structure:
json
{
"title": "Course Title",
"instructor": "Instructor Name",
"lessons": [
{
"title": "Lesson 1 Title",
"duration": "00:30:00"
}
],
"reviews": [
{
"author": "Reviewer Name",
"content": "Review Content",
"date": "2025-02-12"
}
],
"rating": "4.5",
"description": "Course Description"
}
This README provides:
1. Clear installation instructions
Detailed usage examples
Explanation of how to get required cookies
4. Description of output format
Troubleshooting guide
Important notes about usage
Proper disclaimers
You may want to customize the repository URL, license information, and any specific requirements for your implementation.


### Debugging

If the scraper fails to extract data:

1. Check `login_check.html` to verify login status
2. Check `course_response.html` to see the raw page content
3. Look for debug information in the console output

## Error Handling

The scraper will show helpful error messages for common issues:
- Invalid or expired cookies
- Network connection problems
- Missing or invalid course URL
- Failed login verification

## Important Notes

1. Session cookies expire periodically. You'll need to get new cookies by logging in again.
2. The scraper respects MindLuster.com's terms of service by:
   - Using your own account credentials
   - Making requests at a reasonable rate
   - Only accessing content you have permission to view

## Troubleshooting

### Common Issues

1. "Not logged in" error:
   - Your cookies have expired
   - The cookies were copied incorrectly
   - Solution: Get fresh cookies from your browser

2. Empty course data:
   - Check if you can access the course when logged in through your browser
   - Verify the course URL is correct
   - Check course_response.html for the actual page content

3. Missing lessons or reviews:
   - Some courses might not have all content types
   - Verify the content exists when viewing in browser

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Disclaimer

This tool is for educational purposes only. Make sure to comply with MindLuster.com's terms of service and use the scraper responsibly.

mzughbor, Thanks
