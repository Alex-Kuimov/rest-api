<?php
namespace App;

use JetBrains\PhpStorm\NoReturn;

class Response
{
    #[NoReturn] public function JSON($data)
    {
        header('Content-type: application/json');
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit();
    }

    #[NoReturn] public function JSONError($data)
    {
        header('Content-type: application/json');
        echo json_encode(['success' => false, 'message' => $data], JSON_UNESCAPED_UNICODE);
        exit();
    }
}
