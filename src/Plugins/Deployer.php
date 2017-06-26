<?php

namespace Nanga\Plugins;

class Deployer extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga-deploy',
            'channel'            => apply_filters('nanga_updater_nanga-deploy_channel', 'tags'),
            'github_url'         => 'https://github.com/Mallinanga/nanga-deploy',
            'plugin_file'        => 'nanga-deploy/nanga-deploy.php',
            'proper_folder_name' => 'nanga-deploy',
            'slug'               => 'nanga-deploy',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga-deploy/zipball',
        ];
        parent::__construct();
    }
}
