<?php

namespace App\Services;

class MessagesUtil {
    
    public static function error_message(string $message, int $http_code = 400)
    {
        $output_message = ['message' => $message];
        return response()->json($output_message, $http_code);
    }
}
