<?php
namespace App;

use JetBrains\PhpStorm\NoReturn;

class Response
{
    #[NoReturn] public function JSON($data)
    {
        header('Content-type: application/json');
        echo json_encode(['success' => true, 'content' => $data], JSON_UNESCAPED_UNICODE);
        exit();
    }

    #[NoReturn] public function JSONError($data)
    {
        http_response_code(422);
        header('Content-type: application/json');
        echo json_encode(['success' => false, 'message' => $data], JSON_UNESCAPED_UNICODE);
        exit();
    }
}
