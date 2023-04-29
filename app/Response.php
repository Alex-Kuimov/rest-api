<?php
namespace App;

class Response
{
    private Post $post;

    public function __construct()
    {
        $this->post = new Post();
    }

    /**
     * Return data in JSON format
     *
     * @param $data
     */
    public function get($data)
    {
        $res = $data['method'] ? $this->post->{$data['method']}($data) : 'method err';

        header('Content-type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}
