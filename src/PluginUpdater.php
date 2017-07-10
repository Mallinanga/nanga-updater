<?php

namespace Nanga;

class PluginUpdater
{

    protected static $plugins;

    public static function init()
    {
        self::$plugins = [
            'gravityforms'        => __NAMESPACE__ . '\Plugins\GravityForms',
            'nanga'               => __NAMESPACE__ . '\Plugins\Nanga',
            'nanga-contact'       => __NAMESPACE__ . '\Plugins\Contact',
            'nanga-deploy'        => __NAMESPACE__ . '\Plugins\Deploy',
            'nanga-newsletter'    => __NAMESPACE__ . '\Plugins\Newsletter',
            'nanga-notifications' => __NAMESPACE__ . '\Plugins\Notifications',
            'nanga-plugin-test'   => __NAMESPACE__ . '\Plugins\Test',
            'nanga-updater'       => __NAMESPACE__ . '\Plugins\Updater',
            //'woocommerce'       => __NAMESPACE__ . '\Plugins\WooCommerce',
        ];
        add_action('init', [self::class, 'overrideGF'], 100);
        add_action('admin_init', [self::class, 'plugins']);
        add_action('admin_enqueue_scripts', [self::class, 'assets'], 100);
        add_action('admin_bar_menu', [self::class, 'nodes'], 1000);
        add_action('admin_init', [self::class, 'actions']);
        add_action('admin_notices', [self::class, 'notices']);
        add_action('nanga_settings_tab_content_updates', [self::class, 'settings']);
        add_filter('plugin_action_links_nanga-updater/nanga-updater.php', [self::class, 'links']);
    }

    public static function auto()
    {
        add_filter('automatic_updates_is_vcs_checkout', '__return_false', 1);
        add_filter('auto_core_update_send_email', '__return_false');
        add_filter('automatic_updates_send_debug_email', '__return_false');
        /*
        add_filter('auto_core_update_email', function ($email) {
            $email['to'] = get_option('admin_email');

            return $email;
        }, 1);
        */
        add_filter('auto_update_theme', '__return_false', 20, 2);
        add_filter('auto_update_plugin', function ($update, $item) {
            $allowed    = apply_filters('nanga_updater_auto_allowed_plugins', [
                'acf-gallery',
                'acf-options-page',
                'acf-repeater',
                'advanced-custom-fields',
                'advanced-custom-fields-pro',
                'akismet',
                'codepress-admin-columns',
                'gravityforms',
                'imsanity',
                'post-types-order',
                'wordpress-seo',
            ]);
            $disallowed = apply_filters('nanga_updater_auto_disallowed_plugins', [
                'nanga',
            ]);
            $allowed    = [
                'nanga-plugin-test',
            ];
            if (in_array($item->slug, $allowed)) {
                return true;
            }
            if (in_array($item->slug, $disallowed)) {
                return false;
            }

            return $update;
        }, 20, 2);
    }

    public static function overrideGF()
    {
        remove_action('after_plugin_row_gravityforms/gravityforms.php', ['GFAutoUpgrade', 'rg_plugin_row']);
        remove_action('after_plugin_row_gravityforms/gravityforms.php', ['GFForms', 'plugin_row'], 10);
        remove_action('install_plugins_pre_plugin-information', ['GFForms', 'display_changelog'], 9);
        remove_filter('auto_update_plugin', ['GFForms', 'maybe_auto_update'], 10, 2);
        remove_filter('plugins_api', ['GFForms', 'get_addon_info'], 100, 3);
        remove_filter('site_transient_update_plugins', ['GFForms', 'check_update']);
        remove_filter('transient_update_plugins', ['GFForms', 'check_update']);
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

    public static function assets($screen)
    {
        wp_enqueue_style('nanga-updater', NANGA_UPDATER_DIR_URL . 'assets/css/nanga-updater.css', [], NANGA_UPDATER_VERSION, 'all');
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
        $counts = wp_get_update_data();
        $wp_admin_bar->add_menu([
            'href'  => admin_url('update-core.php'),
            'id'    => 'nanga-updates',
            'title' => (empty($counts['title'])) ? __('Updates', 'nanga') : $counts['title'],
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-updates__flush-cache', admin_url('index.php'))),
            'id'     => 'nanga-updates__flush-cache',
            'parent' => 'nanga-updates',
            'title'  => 'Flush Update Cache',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-updates__flush-transients', admin_url('plugins.php?plugin_status=upgrade'))),
            'id'     => 'nanga-updates__flush-transients',
            'parent' => 'nanga-updates',
            'title'  => 'Flush Updater Transients',
        ]);
        $wp_admin_bar->add_node([
            'href'   => wp_nonce_url(add_query_arg('action', 'nanga-updates__force-autoupdate', admin_url('index.php'))),
            'id'     => 'nanga-updates__force-autoupdate',
            'parent' => 'nanga-updates',
            'title'  => 'Force Updates',
        ]);
        $wp_admin_bar->add_node([
            'href'   => admin_url('options-general.php?page=nanga-settings&tab=updates'),
            'id'     => 'nanga-updates__settings',
            'parent' => 'nanga-updates',
            'title'  => 'Settings',
        ]);
        $wp_admin_bar->add_node([
            'href'   => admin_url('update-core.php'),
            'id'     => 'nanga-updates__wordpress',
            'meta'   => ['class' => 'nanga-updates__count'],
            'parent' => 'nanga-updates',
            'title'  => 'WordPress Core' . '<span class="count">' . $counts['counts']['wordpress'] . '</span>',
        ]);
        $wp_admin_bar->add_node([
            'href'   => admin_url('update-core.php'),
            'id'     => 'nanga-updates__translations',
            'meta'   => ['class' => 'nanga-updates__count'],
            'parent' => 'nanga-updates',
            'title'  => 'Translations' . '<span class="count">' . $counts['counts']['translations'] . '</span>',
        ]);
        $wp_admin_bar->add_node([
            'href'   => admin_url('themes.php'),
            'id'     => 'nanga-updates__themes',
            'meta'   => ['class' => 'nanga-updates__count'],
            'parent' => 'nanga-updates',
            'title'  => 'Themes' . '<span class="count">' . $counts['counts']['themes'] . '</span>',
        ]);
        $wp_admin_bar->add_node([
            'href'   => admin_url('plugins.php?plugin_status=upgrade'),
            'id'     => 'nanga-updates__plugins',
            'meta'   => ['class' => 'nanga-updates__count'],
            'parent' => 'nanga-updates',
            'title'  => 'Plugins' . '<span class="count">' . $counts['counts']['plugins'] . '</span>',
        ]);
    }

    public static function actions()
    {
        /*
        global $pagenow;
        if ('index.php' !== $pagenow) {
            return;
        }
        */
        if (isset($_GET['action']) && 'nanga-updates__flush-cache' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce']) && ! doing_action('admin_action_nanga-updates__flush-cache')) {
            add_action('admin_action_nanga-updates__flush-cache', [self::class, 'actionFlushCache']);
        }
        if (isset($_GET['action']) && 'nanga-updates__flush-transients' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce']) && ! doing_action('admin_action_nanga-updates__flush-transients')) {
            add_action('admin_action_nanga-updates__flush-transients', [self::class, 'actionFlushTransients']);
        }
        if (isset($_GET['action']) && 'nanga-updates__force-autoupdate' === $_GET['action'] && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce']) && ! doing_action('admin_action_nanga-updates__force-autoupdate')) {
            add_action('admin_action_nanga-updates__force-autoupdate', [self::class, 'actionForceUpdates']);
        }
    }

    public static function actionFlushCache()
    {
        wp_clean_update_cache();
    }

    public static function actionFlushTransients()
    {
        foreach (self::$plugins as $pluginName => $pluginClass) {
            delete_site_transient($pluginName . '_github_data');
            delete_site_transient($pluginName . '_latest_tag');
        }
    }

    public static function actionForceUpdates()
    {
        if ( ! doing_action('wp_maybe_auto_update')) {
            // do_action('wp_maybe_auto_update');
            wp_maybe_auto_update();
        }
    }

    public static function notices()
    {
        if (did_action('admin_action_nanga-updates__flush-cache')) {
            echo '<div class="notice notice-success is-dismissible"><p>Update cache has been successfully flushed.</p></div>';
        }
        if (doing_action('admin_action_nanga-updates__flush-cache')) {
            echo '<div class="notice notice-warning"><p>Update cache is being currently flushed.</p></div>';
        }
        if (did_action('admin_action_nanga-updates__flush-transients')) {
            echo '<div class="notice notice-success is-dismissible"><p>Transients of latest tags have been successfully flushed.</p></div>';
        }
        if (doing_action('admin_action_nanga-updates__flush-transients')) {
            echo '<div class="notice notice-warning"><p>Transients of latest tags are being currently flushed.</p></div>';
        }
        if (did_action('admin_action_nanga-updates__force-autoupdate')) {
            echo '<div class="notice notice-success is-dismissible"><p>Forcing automatic updates has been triggered successfully.</p></div>';
        }
        if (doing_action('admin_action_nanga-updates__force-autoupdate')) {
            echo '<div class="notice notice-warning"><p>Forcing automatic updates is currently running.</p></div>';
        }
    }

    public static function settings()
    {
        echo '<h2>VG web things Plugin Updater</h2>';
        echo '<p>The following plugins are updated via <strong>VG web things Updater</strong> plugin.</p>';
        echo '<ul>';
        foreach (self::$plugins as $pluginName => $pluginClass) {
            echo '<li><div>' . $pluginName . '</div><p class="description">[TODO] Upgrade channel radio buttons.</p></li>';
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
        delete_site_transient('update_plugins');
        foreach (self::$plugins as $pluginName => $pluginClass) {
            delete_site_transient($pluginName . '_github_data');
            delete_site_transient($pluginName . '_latest_tag');
        }
    }
}
