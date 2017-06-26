<?php

namespace Nanga\Plugins;

class Notifications extends Plugin
{

    public $config = [];

    public function __construct()
    {
        $this->config = [
            'api_url'            => 'https://api.github.com/repos/Mallinanga/nanga-notifications',
            'channel'            => apply_filters('nanga_updater_nanga-notifications_channel', 'tags'),
            'github_url'         => 'https://github.com/Mallinanga/nanga-notifications',
            'plugin_file'        => 'nanga-notifications/nanga-notifications.php',
            'proper_folder_name' => 'nanga-notifications',
            'slug'               => 'nanga-notifications',
            'zip_url'            => 'https://api.github.com/repos/Mallinanga/nanga-notifications/zipball',
        ];
        parent::__construct();
    }
}
