<?php

namespace LBBKR;

class Admin {

    private $db;

    public function __construct() {

        $this->db = new DB();

    }

    public function init() {

        add_action('admin_menu', [$this, 'menu']);

        add_action('admin_init', [$this, 'register_settings']);

    }

    public function menu() {

        add_submenu_page(
            'tools.php',
            __('Login Bot Blocker', 'login-bot-blocker'),
            __('Login Bot Blocker', 'login-bot-blocker'),
            'manage_options',
            'login-bot-blocker',
            [$this, 'settings_page']
        );

    }

    public function register_settings() {

        register_setting('lbbkr_options', 'lbbkr_telegram_token', [
            'sanitize_callback' => 'sanitize_text_field',
        ] );

        register_setting('lbbkr_options', 'lbbkr_telegram_chat_id', [
            'sanitize_callback' => 'sanitize_text_field',
        ] );

        register_setting('lbbkr_options', 'lbbkr_enabled', [
            'sanitize_callback' => [ $this, 'lbbkr_sanitize_checkbox' ],
            'default'           => '0'
        ] );

    }

    public function lbbkr_sanitize_checkbox( $value ) {

        return in_array( $value, ['1'], true ) ? '1' : '0';

    }

    public function settings_page() {

        if (isset($_GET['delete_ip'])) {

            check_admin_referer('lbbkr_delete_ip');

            $this->db->delete_ip( sanitize_text_field( wp_unslash( $_GET['delete_ip'] ) ) );

        }

        if (isset($_POST['lbbkr_clear_all']) && check_admin_referer('lbbkr_clear_all')) {

            $this->db->clear_all();
            
        }

        $ips = $this->db->get_blocked_ips();

        ?>
        <div class="wrap">
            <h1><?php echo esc_html( __( 'Login Bot Blocker', 'login-bot-blocker' ) ); ?></h1>
            <?php settings_errors(); ?>
            <form method="post" action="options.php" autocomplete="off">
                <?php
                settings_fields('lbbkr_options');
                do_settings_sections('lbbkr_options');
                ?>
                <table class="form-table">
                    <tr><th><?php esc_html_e( 'Telegram Bot Token', 'login-bot-blocker' ); ?></th><td><input type="password" name="lbbkr_telegram_token" value="<?php echo esc_attr(get_option('lbbkr_telegram_token')) ?>" size="50" placeholder="<?php echo esc_html( __( 'for example', 'login-bot-blocker' ) ); ?>: 0123456789:TOKEN_CHARS" autocomplete="new-password"></td></tr>
                    <tr><th><?php echo esc_html( __( 'Telegram Chat ID', 'login-bot-blocker' ) ); ?></th><td><input type="text" name="lbbkr_telegram_chat_id" value="<?php echo esc_attr(get_option('lbbkr_telegram_chat_id')) ?>" placeholder="<?php echo esc_html( __( 'for example', 'login-bot-blocker' ) ); ?>: -9876543210" size="50"></td></tr>
                    <tr><th><?php echo esc_html( __( 'Enable Notifications', 'login-bot-blocker' ) ); ?></th><td><input type="checkbox" name="lbbkr_enabled" value="1" <?php checked( get_option('lbbkr_enabled', '0'), '1' ); ?>></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <h2><?php echo esc_html( __( 'Blocked IPs', 'login-bot-blocker' ) ); ?></h2>
            <form method="post">
                <?php wp_nonce_field('lbbkr_clear_all'); ?>
                <button class="button button-secondary" name="lbbkr_clear_all"><?php echo esc_html( __( 'Clear All', 'login-bot-blocker' ) ); ?></button>
            </form>
            <ul>
                <?php foreach ($ips as $row): ?>
                    <li>
                    <?php echo esc_html($row->ip) ?> — <?php echo esc_html($row->blocked_at) ?>
                        <a href="<?php echo esc_url( wp_nonce_url( add_query_arg('delete_ip', $row->ip), 'lbbkr_delete_ip' ) ); ?>">
                        <?php echo esc_html( __( 'Delete', 'login-bot-blocker' ) ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}
