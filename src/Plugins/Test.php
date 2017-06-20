<?php

namespace Nanga\Plugins;

use Nanga\Plugin;

class Test extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga-plugin-test',
            'channel'            => 'tags',
            'github_url'         => 'https://github.com/Mallinanga/nanga-plugin-test',
            'plugin_file'        => 'nanga-plugin-test/nanga-plugin-test.php',
            'proper_folder_name' => 'nanga-plugin-test',
            'slug'               => 'nanga-plugin-test',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga-plugin-test/zipball',
        ];
        parent::__construct();
    }
}
