<?php
namespace App;

class Validator
{
    /**
     * Available methods array
     *
     * @return array
     */
    public function availableMethods(): array
    {
        return ['get', 'update', 'create', 'delete', 'login'];
    }

    /**
     * Validate POST data
     *
     * @param $items
     * @return array
     */
    public function postData($items): array
    {
        $arr = [];

        foreach ($items as $key => $item) {
            if ($key === 'method') {
                continue;
            }

            if ($key !== 'meta') {
                $arr[$key] = $this->sanitizeData($item);
            }

            if ($key === 'meta') {
                $arr[$key] = array_map(function ($prop) {
                    return $this->sanitizeData($prop);
                }, $item);
            }
        }

        return $arr;
    }

    /**
     * Sanitize data
     *
     * @param $str
     * @return string
     */
    public function sanitizeData($str): string
    {
        $str = trim($str);
        $str = stripslashes($str);
        $str = strip_tags($str);
        $str = htmlspecialchars($str);
        return str_replace(["\r", "\n", chr(0)], '', $str);
    }
}
