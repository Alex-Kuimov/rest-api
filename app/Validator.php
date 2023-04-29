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
        return ['get', 'update', 'create', 'delete'];
    }

    public function postData($items): array
    {
        $arr = [];
        foreach ($items as $key => $item) {
            $arr[$key] = $this->sanitizeData($item);
        }
        return $arr;
    }

    public function sanitizeData($str): string
    {
        $str = trim($str);
        $str = stripslashes($str);
        $str = strip_tags($str);
        $str = htmlspecialchars($str);
        return str_replace(["\r", "\n", chr(0)], '', $str);
    }
}
