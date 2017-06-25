<?php
/**
 * Author URI: https://github.com/Mallinanga/
 * Author: Panos Paganis
 * Description: VG web things Updater.
 * Plugin Name: VG web things Updater
 * Version: 1.3.0
 */

defined('WPINC') || die;

define('NANGA_UPDATER_VERSION', '1.3.0');

require_once(dirname(__FILE__) . '/vendor/autoload.php');

register_activation_hook(__FILE__, ['\Nanga\PluginUpdater', 'activate']);
register_deactivation_hook(__FILE__, ['\Nanga\PluginUpdater', 'deactivate']);

\Nanga\PluginUpdater::init();
\Nanga\PluginUpdater::auto();
