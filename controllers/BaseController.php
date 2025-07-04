<?php

require(__DIR__ . "/../connection/connection.php");
require(__DIR__ . "/../services/ResponseService.php");


abstract class BaseController
{
    protected $mysqli;

    public function __construct()
    {
        global $mysqli;
        $this->mysqli = $mysqli;
    }
    protected function success($payload, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo \ResponseService::success_response($payload, $status);
    }

    protected function error($message, $status = 400)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo \ResponseService::error_response($message, $status);
    }
}
