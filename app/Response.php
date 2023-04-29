<?php
namespace App;

class Response
{
    /**
     * Return data in JSON format
     *
     * @param $data
     */
    public function getJSON($data)
    {
        $method = $data['method'];

        $post = new Post($data);

        $res = $method ? $post->{$method}() : 'method err';

        header('Content-type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}
