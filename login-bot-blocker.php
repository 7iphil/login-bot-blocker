<?php
/**
 * Plugin Name:       Login Bot Blocker
 * Description:       Blocks login bots using a honeypot trap and sends alerts via Telegram.
 * Version:           1.0
 * Author:            iPhil
 * Author URI:        https://iphil.top
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       login-bot-blocker
 * Requires at least: 5.3
 * Requires PHP:      7.2
 * Tested up to:      6.8.1
 */

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/plugin.php';

add_filter('plugin_row_meta', 'lbbkr_plugin_row_meta', 10, 2);
 
function lbbkr_plugin_row_meta($links, $file) {

    if ($file === plugin_basename(__FILE__)) {

        $links[] = '<a href="' . esc_url(admin_url('tools.php?page=login-bot-blocker')) . '">Settings</a>';

    }

    return $links;

}

function lbbkr_run_plugin() {

    $plugin = new \LBBKR\Plugin();

    $plugin->init();
    
}

add_action('plugins_loaded', 'lbbkr_run_plugin');