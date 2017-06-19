<?php

namespace Nanga;

use Nanga\Plugins\GravityForms;
use Nanga\Plugins\Test;
use Nanga\Plugins\Updater;

class PluginUpdater
{
    protected $plugins;

    public function __construct()
    {
        $plugins = [];
        $this->plugins = apply_filters('nanga_updater_exclude_plugins', $plugins);
    }

    public static function init()
    {
        add_action('admin_init', [self::class, 'plugins']);
    }

    public static function activate()
    {
        delete_site_transient('update_plugins');
        delete_site_transient('gravityforms_github_data');
        delete_site_transient('gravityforms_latest_tag');
        delete_site_transient('nanga-deploy_github_data');
        delete_site_transient('nanga-deploy_latest_tag');
        delete_site_transient('nanga-plugin-test_github_data');
        delete_site_transient('nanga-plugin-test_latest_tag');
        delete_site_transient('nanga_github_data');
        delete_site_transient('nanga_latest_tag');
        delete_site_transient('woocommerce_github_data');
        delete_site_transient('woocommerce_latest_tag');
    }

    public static function deactivate()
    {
        // delete_site_transient('update_plugins');
        delete_site_transient('gravityforms_github_data');
        delete_site_transient('gravityforms_latest_tag');
        delete_site_transient('nanga-deploy_github_data');
        delete_site_transient('nanga-deploy_latest_tag');
        delete_site_transient('nanga-plugin-test_github_data');
        delete_site_transient('nanga-plugin-test_latest_tag');
        delete_site_transient('nanga_github_data');
        delete_site_transient('nanga_latest_tag');
        delete_site_transient('woocommerce_github_data');
        delete_site_transient('woocommerce_latest_tag');
    }

    public static function plugins()
    {
        new GravityForms();
        new Test();
        new Updater();
    }
}
