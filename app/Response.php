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
        $type = $data['type'];

        if (empty($type)) {
            $this->returnJSON(APP_NAME . ' app. Author: ' . APP_AUTHOR . '. Ver: ' . APP_VER);
            exit();
        }

        if (empty($method)) {
            $this->returnJSON('method err');
            exit();
        }

        if ($type === 'users') {
            $user = new User($data);
            $this->returnJSON($user->{$method}());
            exit();
        }

        if ($type === 'groups') {
            //
        }

        if ($type !== 'users') {
            $post = new Post($data);
            $this->returnJSON($post->{$method}());
            exit();
        }
    }

    private function returnJSON($data)
    {
        header('Content-type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
