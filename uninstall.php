<?php
/**
 * Uninstallation script for the Login Bot Blocker plugin.
 *
 * @package LBBKR
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly.
}

global $wpdb;

// Define table name using WordPress functions and sanitize it.
$table_name = sanitize_key($wpdb->prefix . 'login_bot_blocker');

// phpcs:disable WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
    $wpdb->prepare(
        "DROP TABLE IF EXISTS %s",
        $table_name
    )
);
// phpcs:enable WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

// Delete plugin options from database.
delete_option('lbbkr_telegram_token');
delete_option('lbbkr_telegram_chat_id');
delete_option('lbbkr_enabled');

// For multisite compatibility.
delete_site_option('lbbkr_telegram_token');
delete_site_option('lbbkr_telegram_chat_id');
delete_site_option('lbbkr_enabled');
