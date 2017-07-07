<?php
/**
 * Author URI: https://github.com/Mallinanga/
 * Author: Panos Paganis
 * Description: VG web things Updater.
 * Plugin Name: VG web things Updater
 * Version: 1.4.1
 */

defined('WPINC') || die;
<<<<<<< HEAD
define('NANGA_UPDATER_VERSION', '1.4.1');
if (defined('NANGA_VIP_URL')) {
    define('NANGA_UPDATER_DIR_URL', NANGA_VIP_URL . 'plugins/nanga-updater/');
} else {
    define('NANGA_UPDATER_DIR_URL', plugin_dir_url(__FILE__));
}
=======

define('NANGA_UPDATER_VERSION', '1.4.0');
if (defined('NANGA_VIP_URL')) {
    define('NANGA_UPDATER_DIR_URL', plugin_dir_url(NANGA_VIP_URL . 'plugins/nanga-updater/'));
} else {
    define('NANGA_UPDATER_DIR_URL', plugin_dir_url(__FILE__));
}

>>>>>>> a52cd81e43245bc1aebd4fd5801fc1d0a2c7802c
require_once(dirname(__FILE__) . '/vendor/autoload.php');
register_activation_hook(__FILE__, ['\Nanga\PluginUpdater', 'activate']);
register_deactivation_hook(__FILE__, ['\Nanga\PluginUpdater', 'deactivate']);
\Nanga\PluginUpdater::init();
// \Nanga\PluginUpdater::auto();
