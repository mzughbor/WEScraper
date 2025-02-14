# MindLuster Course Scraper

A PHP-based web scraper for extracting course information from [MindLuster](https://www.mindluster.com). This tool allows you to download course details, including title, instructor, lessons, reviews, ratings, and descriptions.

## Prerequisites

Ensure your environment meets the following requirements:

- PHP 7.4 or higher
- Composer
- cURL extension for PHP
- A valid MindLuster.com account with active session cookies

## Installation

1. Clone this repository:
   ```bash
   git clone https://github.com/mzughbor/WEScraper.git
   cd course-scraper
   ```

2. Install dependencies using Composer:
   ```bash
   composer install
   ```

## Usage

### Retrieving Required Cookies

Before using the scraper, obtain your session cookies from MindLuster.com:

1. Log in to [MindLuster](https://www.mindluster.com) using your web browser.
2. Open Developer Tools (F12 in most browsers).
3. Navigate to the **Application** or **Storage** tab.
4. Locate **Cookies** under `www.mindluster.com`.
5. Copy the values for the following cookies:
   - `XSRF-TOKEN`
   - `laravel_session`
   - `FCNEC` (optional)

### Running the Scraper

Execute the following command with your cookies:

```bash
php scrape.php \
--url="https://www.mindluster.com/certificate/[COURSE_ID]/[COURSE_NAME]" \
--xsrf-token="your_xsrf_token_here" \
--laravel-session="your_laravel_session_here" \
--fcnec="your_fcnec_here"
```

#### Example:
```bash
php scrape.php \
--url="https://www.mindluster.com/certificate/16207/Blockchain-development-video" \
--xsrf-token="eyJpdiI6IktqY0d1..." \
--laravel-session="MIKtuuCRQNHiOqt..." \
--fcnec="%5B%5B%22AKsRol9p..."
```

### Output

The scraper performs the following steps:
1. Verifies login status.
2. Scrapes the course page.
3. Saves the results to a JSON file (`course_data_[TIMESTAMP].json`).

#### Example Output Structure:
```json
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
```

## Debugging

If the scraper fails to extract data, try the following:

1. Check `login_check.html` to verify login status.
2. Examine `course_response.html` for raw page content.
3. Look for error messages in the console output.

## Error Handling

The scraper provides helpful error messages for common issues:

- **Invalid or expired cookies**: Reauthenticate and obtain new cookies.
- **Network connection issues**: Ensure a stable internet connection.
- **Incorrect course URL**: Verify that the provided URL is valid.
- **Failed login verification**: Double-check session cookies.

## Important Notes

- **Session cookies expire periodically**. Retrieve fresh cookies as needed.
- The scraper respects MindLuster's terms of service by:
  - Using your own account credentials.
  - Making requests at a reasonable rate.
  - Accessing only content that you have permission to view.

## Troubleshooting

### Common Issues and Solutions

1. **"Not logged in" error**
   - Cookies may be expired or copied incorrectly.
   - **Solution**: Obtain new cookies from your browser.

2. **Empty course data**
   - Verify access to the course while logged into your browser.
   - Ensure the course URL is correct.
   - Check `course_response.html` for the retrieved content.

3. **Missing lessons or reviews**
   - Some courses may not have all content types.
   - Check the course page to confirm the available information.

## License

This project is licensed under the MIT License. See the `LICENSE` file for more details.

## Disclaimer

This tool is for **educational purposes only**. Users must comply with MindLuster.com's terms of service and use the scraper responsibly.

---
**Maintainer:** mzughbor

