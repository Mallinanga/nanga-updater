<?php

namespace Nanga\Plugins;

class Nanga extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga',
            'channel'            => apply_filters('nanga_updater_nanga_channel', 'tags'),
            'github_url'         => 'https://github.com/Mallinanga/nanga',
            'plugin_file'        => 'nanga/nanga.php',
            'proper_folder_name' => 'nanga',
            'slug'               => 'nanga',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga/zipball',
        ];
        parent::__construct();
    }
}
