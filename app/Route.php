<?php
namespace App;

class Route
{
    private String $url;
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    public function routing()
    {
        $this->url = $this->getURL();
    }

    /**
     * Creating data array
     *
     * @return array
     */
    public function getMeta(): array
    {
        return [
            'type' => $this->getType($this->url),
            'method' => $this->getMethod(),
            'content' => $this->getData(),
        ];
    }

    /**
     * Get data type
     *
     * @param $url
     * @return String
     */
    private function getType($url):String
    {
        $type = str_replace(APP_URL, '', $url);
        return str_replace('/', '', $type);
    }

    /**
     * Get method name
     *
     * @return String|null
     */
    private function getMethod(): ?String
    {
        $method = $_POST['method'] ?? null;

        return in_array($method, $this->validator->availableMethods(), true)? $method: null;
    }

    /**
     * Get POST data
     *
     * @return array
     */
    private function getData():array
    {
        return $this->validator->postData($_POST);
    }

    /**
     * Get url string
     *
     * @return String
     */
    private function getURL():String
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
