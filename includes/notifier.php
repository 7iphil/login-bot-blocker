<?php
namespace LBBKR;

class Notifier {

    public function get_geolocation( $ip ) {
    
        $response = wp_remote_get( "http://ip-api.com/json/" . esc_html( $ip ) );
    
        if ( is_wp_error( $response ) ) {
        
            return false;
    
        }
    
        $body = wp_remote_retrieve_body( $response ); 
    
        $data = json_decode( $body, true );
    
        return is_array( $data ) ? $data : false;
    
    }

    public function send($message) {

        $token = get_option('lbbkr_telegram_token');

        $chat_id = get_option('lbbkr_telegram_chat_id');

        if (!$token || !$chat_id) {

            return;

        }

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $args = [
            'body' => [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ],
            'timeout' => 10,
        ];

        wp_remote_post($url, $args);

    }
    
}
