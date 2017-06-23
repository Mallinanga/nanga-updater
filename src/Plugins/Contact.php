<?php

namespace Nanga\Plugins;

use Nanga\Plugin;

class Contact extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga-contact',
            'channel'            => 'tags',
            'github_url'         => 'https://github.com/Mallinanga/nanga-contact',
            'plugin_file'        => 'nanga-contact/nanga-contact.php',
            'proper_folder_name' => 'nanga-contact',
            'slug'               => 'nanga-contact',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga-contact/zipball',
        ];
        parent::__construct();
    }
}
