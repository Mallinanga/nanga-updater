<?php

namespace Nanga\Plugins;

use Nanga\Plugin;

class WooCommerce extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/woocommerce/woocommerce',
            'channel'            => 'releases',
            'github_url'         => 'https://github.com/woothemes/woocommerce',
            'plugin_file'        => 'woocommerce/woocommerce.php',
            'proper_folder_name' => 'woocommerce',
            'slug'               => 'woocommerce',
            'zip_url'            => 'https://api.github.com/repos/woocommerce/woocommerce/zipball',
        ];
        parent::__construct();
    }
}
