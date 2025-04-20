<?php
// utils/ApiUtils.php - Common utility functions for API operations

class ApiUtils
{
    /**
     * Validates if required fields are present in the data array
     *
     * @param array $data The data array to check
     * @param array $requiredFields Array of required field names
     * @return array [isValid, missingFields]
     */
    public static function validateRequiredFields($data, $requiredFields)
    {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        return [
            'isValid' => empty($missingFields),
            'missingFields' => $missingFields
        ];
    }

    /**
     * Sanitizes a string for database operations
     *
     * @param PDO $pdo Database connection
     * @param string $string String to sanitize
     * @return string Sanitized string
     */
    public static function sanitizeString($pdo, $string)
    {
        if ($string === null) {
            return null;
        }
        return trim($string);
    }

    /**
     * Converts a string to an integer safely
     *
     * @param mixed $value Value to convert
     * @param int $default Default value if conversion fails
     * @return int Converted integer
     */
    public static function toInt($value, $default = null)
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $int = filter_var($value, FILTER_VALIDATE_INT);
        return $int !== false ? $int : $default;
    }

    /**
     * Sends a JSON response with status code
     *
     * @param int $statusCode HTTP status code
     * @param array $data Data to send
     */
    public static function sendJsonResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Logs an API error
     *
     * @param string $message Error message
     * @param mixed $context Additional context
     */
    public static function logApiError($message, $context = null)
    {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message,
            'context' => $context
        ];

        // You can implement your preferred logging method here
        // For example, write to a log file or use error_log()
        error_log(json_encode($logEntry));
    }
}