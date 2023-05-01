<?php
namespace App;

class Response
{
    public function JSON($data)
    {
        header('Content-type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
