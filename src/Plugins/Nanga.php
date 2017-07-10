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
        add_action('after_plugin_row_nanga/nanga.php', [$this, 'warning'], 10, 3);
    }

    public function warning($file, $data, $status)
    {
        if (version_compare($data['Version'], '2.0.0', '<')) {
            echo '</tr><tr class="plugin-update-tr active"><td colspan="5" class="plugin-update" style="box-shadow:none;"><div class="update-message notice inline notice-error notice-alt" style="margin-top:15px;">';
            echo '<p>Versions above <em>2.0.0</em> include major changes. Please make sure you understand all the implications before upgrading this plugin.</p>';
            echo '<p>If you decide to upgrade the plugin, please do it on the <a href="' . admin_url('plugins.php?plugin_status=upgrade') . '">plugins page</a>.</p>';
            echo '</div></td>';
        }
    }
}
