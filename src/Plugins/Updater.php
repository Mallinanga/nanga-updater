<?php

namespace Nanga\Plugins;

use Nanga\Plugin;

class Updater extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga-updater',
            'channel'            => 'tags',
            'github_url'         => 'https://github.com/Mallinanga/nanga-updater',
            'plugin_file'        => 'nanga-updater/nanga-updater.php',
            'proper_folder_name' => 'nanga-updater',
            'slug'               => 'nanga-updater',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga-updater/zipball',
        ];
        parent::__construct();
    }
}
