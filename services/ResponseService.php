<?php

class ResponseService {

    public static function success_response($payload, $status = 200){
        $response = [];
        $response["status"] = $status;
        $response["payload"] = $payload;
        return json_encode($response);
    }

    public static function error_response($message, $status = 400){
        $response = [];
        $response["status"] = $status;
        $response["error"] = $message;
        return json_encode($response);
    }


}