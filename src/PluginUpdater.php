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
            //'nanga'             => __NAMESPACE__ . '\Plugins\Nanga',
            'gravityforms'        => __NAMESPACE__ . '\Plugins\GravityForms',
            'nanga-contact'       => __NAMESPACE__ . '\Plugins\Contact',
            'nanga-deploy'        => __NAMESPACE__ . '\Plugins\Deploy',
            'nanga-newsletter'    => __NAMESPACE__ . '\Plugins\Newsletter',
            'nanga-notifications' => __NAMESPACE__ . '\Plugins\Notifications',
            'nanga-plugin-test'   => __NAMESPACE__ . '\Plugins\Test',
            'nanga-updater'       => __NAMESPACE__ . '\Plugins\Updater',
            'woocommerce'         => __NAMESPACE__ . '\Plugins\WooCommerce',
        ];
        add_action('admin_init', [self::class, 'plugins']);
        add_filter('plugin_action_links_nanga-updater/nanga-updater.php', [self::class, 'links']);
        add_action('admin_bar_menu', [self::class, 'nodes'], 100);
        add_action('admin_init', [self::class, 'actions'], 1);
        add_action('admin_notices', [self::class, 'notices']);
        add_action('nanga_settings_tab_content_updates', [self::class, 'settings']);
    }

    public static function plugins()
    {
        $plugins = apply_filters('nanga_updater_exclude_plugins', self::$plugins);
        foreach ($plugins as $pluginName => $pluginClass) {
            if ( ! class_exists($pluginClass)) {
                continue;
            }
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
        add_filter('auto_core_update_send_email', '__return_false');
        add_filter('automatic_updates_send_debug_email', '__return_false');
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

    public static function links($links)
    {
        if ( ! class_exists('\Nanga\Settings')) {
            return $links;
        }

        return array_merge(['advanced_settings' => '<a href="' . admin_url('options-general.php?page=nanga-settings&tab=updates') . '">' . __('Settings', 'nanga-updater') . '</a>'], $links);
    }

    public static function nodes($wp_admin_bar)
    {
        if ( ! current_user_can('manage_options') || defined('NANGA_EXTERNAL')) {
            return;
        }
        $wp_admin_bar->add_menu([
            'id'    => 'nanga-updates',
            'title' => 'Updates',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(admin_url()),
            'id'     => 'nanga-updates__clean-cache',
            'parent' => 'nanga-updates',
            'title'  => 'Clean Update Cache',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(admin_url()),
            'id'     => 'nanga-updates__force',
            'parent' => 'nanga-updates',
            'title'  => 'Force Updates',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(admin_url()),
            'id'     => 'nanga-updates__autoupdate',
            'parent' => 'nanga-updates',
            'title'  => 'Maybe Autoupdate',
        ]);
    }

    public static function actions()
    {
        /*
        wp_clean_update_cache();
        if ( ! doing_action('wp_maybe_auto_update')) {
            // do_action('wp_maybe_auto_update');
            wp_maybe_auto_update();
        }
        */
    }

    public static function notices()
    {
        echo '<div class="notice notice-success is-dismissible"><p>This is an admin notice.</p></div>';
    }

    public static function settings()
    {
        echo '<h2>VG web things Plugin Updater</h2>';
        echo '<p>The following plugins are updated via VG web things Updater plugin.</p>';
        echo '<ul>';
        foreach (self::$plugins as $pluginName => $pluginClass) {
            echo '<li>' . $pluginName . '</li>';
        }
        echo '</ul>';
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
