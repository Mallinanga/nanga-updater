<?php

namespace Nanga\Plugins;

use Nanga\Plugin;

class GravityForms extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/wp-premium/gravityforms',
            'channel'            => 'tags',
            'github_url'         => 'https://github.com/wp-premium/gravityforms',
            'plugin_file'        => 'gravityforms/gravityforms.php',
            'proper_folder_name' => 'gravityforms',
            'slug'               => 'gravityforms',
            'zip_url'            => 'https://api.github.com/repos/wp-premium/gravityforms/zipball',
        ];
        parent::__construct();
    }
}
