<?php

namespace LBBKR;

class FormManipulator {
    public function init_hooks(): void {
        add_filter('login_form_middle', [$this, 'inject_honeypot_field'], 10);
        add_action('login_enqueue_scripts', [$this, 'disable_browser_autofill'], 10);
        add_action('login_head', [$this, 'start_output_buffer'], 0);
        add_action('login_footer', [$this, 'end_output_buffer'], PHP_INT_MAX);
    }

    public function inject_honeypot_field(): string {
        return '<input type="text" name="lbbkr_hp" id="user_login" autocomplete="off" style="display:none;">';
    }

    public function disable_browser_autofill(): void {
        echo '<style>
            input[name="lbbkr_hp"] { display: none !important; }
            input#user_login_fake, input#user_pass_fake { autocomplete: off !important; }
        </style>';
    }

    public function start_output_buffer(): void {
        ob_start([$this, 'replace_login_input_ids']);
    }

    public function end_output_buffer(): void {
        ob_end_flush();
    }

    public function replace_login_input_ids(string $content): string {
        $content = str_replace('id="user_login"', 'id="user_login_fake"', $content);
        $content = str_replace('id="user_pass"', 'id="user_pass_fake"', $content);
        return $content;
    }
}
