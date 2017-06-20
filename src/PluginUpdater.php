<?php

namespace Nanga;

class PluginUpdater
{

    protected static $plugins;
    protected static $allowed;
    protected static $disallowed;

    public static function init()
    {
        self::$plugins = [
            'gravityforms'      => __NAMESPACE__ . '\Plugins\GravityForms',
            'nanga-plugin-test' => __NAMESPACE__ . '\Plugins\Test',
            'nanga-updater'     => __NAMESPACE__ . '\Plugins\Updater',
            'woocommerce'       => __NAMESPACE__ . '\Plugins\WooCommerce',
        ];
        add_action('admin_init', [self::class, 'plugins']);
    }

    public static function plugins()
    {
        $plugins = apply_filters('nanga_updater_exclude_plugins', self::$plugins);
        foreach ($plugins as $pluginName => $pluginClass) {
            new $pluginClass();
        }
    }

    public static function auto()
    {
        self::$allowed    = [
            'acf-gallery',
            'acf-options-page',
            'acf-repeater',
            'advanced-custom-fields',
            'advanced-custom-fields-pro',
            'akismet',
            'codepress-admin-columns',
            'gravityforms',
            'imsanity',
            'jigsaw',
            'post-types-order',
            'user-role-editor',
            'wordpress-seo',
        ];
        self::$disallowed = [
            'nanga',
        ];
        $allowed          = apply_filters('nanga_updater_auto_allowed_plugins', self::$allowed);
        $disallowed       = apply_filters('nanga_updater_auto_disallowed_plugins', self::$disallowed);
        if (defined('WP_ENV') && 'development' === WP_ENV) {
            add_filter('automatic_updates_is_vcs_checkout', '__return_false', 1);
        }
        add_filter('automatic_updates_send_debug_email', '__return_true');
        add_filter('auto_core_update_email', function ($email) {
            $email['to'] = get_option('admin_email');

            return $email;
        }, 1);
        add_filter('auto_update_theme', function ($update, $item) {
            error_log(print_r($update, true));
            error_log(print_r($item, true));

            return $update;
        }, 20, 2);
        add_filter('auto_update_plugin', function ($update, $item) {
            error_log(print_r($update, true));
            error_log(print_r($item, true));

            return $update;
        }, 20, 2);
        /*
        if (defined('NANGA_PLAYGROUND') && NANGA_PLAYGROUND) {
            add_filter('auto_update_plugin', function ($update, $item) {
                if (in_array($item->slug, self::$disallowed_plugins)) {
                    return false;
                }

                return true;
            }, 20, 2);
        } else {
            add_filter('auto_update_plugin', function ($update, $item) {
                if (in_array($item->slug, self::$allowed_plugins)) {
                    return true;
                }

                return false;
            }, 20, 2);
        }
        */
    }

    public static function activate()
    {
        delete_site_transient('update_plugins');
        foreach (self::$plugins as $pluginName => $pluginClass) {
            delete_site_transient($pluginName . '_github_data');
            delete_site_transient($pluginName . '_latest_tag');
        }
    }

    public static function deactivate()
    {
        // delete_site_transient('update_plugins');
        foreach (self::$plugins as $pluginName => $pluginClass) {
            delete_site_transient($pluginName . '_github_data');
            delete_site_transient($pluginName . '_latest_tag');
        }
    }
}
