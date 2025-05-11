<?php

namespace LBBKR;

class Blocker {
    
    private $db;
    
    private $notifier;

    public function __construct() {

        $this->db = new DB();
        
        $this->notifier = new Notifier();

    }

    public function init() {

        add_action('login_form', [$this, 'add_honeypot']);

        add_filter('authenticate', [$this, 'check_honeypot'], 30, 3);

    }

    public function add_honeypot() {

        echo '<p style="display:none">
                <label for="password">Password</label>
                <input type="text" name="password" id="password" type="password" autocomplete="off">
              </p>';
    }

    public function check_honeypot($user, $username, $password) {
        
        $ip = isset($_SERVER['REMOTE_ADDR']) 
            ? filter_var(wp_unslash($_SERVER['REMOTE_ADDR']), FILTER_VALIDATE_IP) 
            : 'unknown';

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Honeypot verification does not require a nonce, no UI.
        $trap_filled = isset($_POST['password']) && !empty($_POST['password']);

        if ($this->db->is_blocked($ip)) {

            return new \WP_Error('blocked', __('Access denied.', 'login-bot-blocker'));

        }

        if ($trap_filled) {

            $this->db->block_ip($ip);

            if (get_option('lbbkr_enabled') === '1') {

                $geo = $this->notifier->get_geolocation($ip);
                $url = site_url();
                $message  = "🛡️ *Bot blocked*\n";
                $message .= "🔗 *URL*: `" . esc_url_raw( $url ) . "`\n";
                $message .= "👤 *User*: `" . esc_html( $username ) . "`\n";
                $message .= "🌍 *IP*: `" . esc_html( $ip ) . "`\n";
                if ( $geo && is_array( $geo ) && $geo['status'] === 'success' ) {
                    $message .= "🧭 *Location*: `" . esc_html( $geo['city'] . ', ' . $geo['country'] ) . "`\n";
                }
                // Handle user agent
                $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'Not detected';
                $message .= "🕒 *Time*: `" . current_time( 'mysql' ) . "`\n";
                $message .= "ℹ️ *User agent*: `" . esc_html( $user_agent ) . "`";                
            
                $this->notifier->send($message);

            }

            return new \WP_Error('bot_detected', __('Access denied.', 'login-bot-blocker'));

        }

        return $user;

    }

}
