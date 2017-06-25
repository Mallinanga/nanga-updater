<?php

namespace Nanga\Plugins;

class Newsletter extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga-newsletter',
            'channel'            => 'tags',
            'github_url'         => 'https://github.com/Mallinanga/nanga-newsletter',
            'plugin_file'        => 'nanga-newsletter/nanga-newsletter.php',
            'proper_folder_name' => 'nanga-newsletter',
            'slug'               => 'nanga-newsletter',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga-newsletter/zipball',
        ];
        parent::__construct();
    }
}
