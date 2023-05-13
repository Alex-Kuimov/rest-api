<?php
namespace App;

class Plugins
{
    private string $dir = '../plugins/';

    public function init()
    {
        $folders = array_diff(scandir($this->dir), ['.', '..']);
        foreach ($folders as $folder) {
            if (is_dir($this->dir . '/' . $folder)) {
                $plugin  = '../plugins/'.$folder .'/index.php';
                require_once $plugin;
            }
        }
    }
}
