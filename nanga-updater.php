<?php
/**
 * Author URI: http://www.vgwebthings.com/
 * Author: VG web things
 * Description: VG web things Updater.
 * Plugin Name: VG web things Updater
 * Version: 1.0.0
 */
if ( ! defined('ABSPATH')) {
    exit;
}
define('NANGA_UPDATER_VERSION', '1.0.0');

require_once(dirname(__FILE__) . '/vendor/autoload.php');

register_activation_hook(__FILE__, ['\Nanga\PluginUpdater', 'activate']);
register_deactivation_hook(__FILE__, ['\Nanga\PluginUpdater', 'deactivate']);

\Nanga\PluginUpdater::init();
\Nanga\PluginUpdater::auto();
