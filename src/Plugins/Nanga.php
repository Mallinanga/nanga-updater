<?php

namespace Nanga\Plugins;

class Nanga extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga',
            'channel'            => 'releases',
            'github_url'         => 'https://github.com/Mallinanga/nanga',
            'plugin_file'        => 'nanga/nanga.php',
            'proper_folder_name' => 'nanga',
            'slug'               => 'nanga',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga/zipball',
        ];
        parent::__construct();
    }
}
