<?php
/**
 * JSON Response Helper
 */

class Response {
    public static function json($success, $message, $data = null) {
        header('Content-Type: application/json');
        
        $response = [
            'success' => $success,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    public static function success($message, $data = null) {
        self::json(true, $message, $data);
    }
    
    public static function error($message, $data = null) {
        self::json(false, $message, $data);
    }
}
?>
