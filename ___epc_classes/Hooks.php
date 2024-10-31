<?php

namespace ___epc_classes;

require_once dirname(__FILE__) . '/Options.php';

use ___epc_classes\Options;

abstract class Hooks
{
    static function register_activation_hook()
    {
        if (function_exists('__essert_privacy_compliance_p_app_init')) {
            die("You must Deactivate 'Essert Privacy Compliance (Partner)' plugin to use this plugin.");
        }
        Options::setOptions();
        do_action('track_event', ['event' => "PLUGIN:ACTIVATED"]);
    }

    static function register_deactivation_hook()
    {
        do_action('track_event', ['event' => "PLUGIN:DEACTIVATED"]);
    }

    static function register_uninstall_hook()
    {
        Options::deleteOptions();
        do_action('track_event', ['event' => "PLUGIN:UNINSTALLED"]);
    }

    static function _track_event(array $payload = [])
    {
        wp_remote_post("https://pe.essert.io/api/essert-privacy-compliance-app/done", [
            'body' => [
                'event_name' => $payload['event'],
                'identity' => [
                    '$distinct_id' => isset($_SERVER['HTTP_HOST']) ? sanitize_text_field($_SERVER['HTTP_HOST']) : ''
                ],
                'props' => array_merge([
                    'ip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
                    '$os' => isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM']) ? sanitize_text_field($_SERVER['HTTP_SEC_CH_UA_PLATFORM']) : '',
                    'device' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
                    'URI Path' => isset($_SERVER['REQUEST_URI']) ? sanitize_text_field($_SERVER['REQUEST_URI']) : '',
                    'Plugin Name' => sanitize_text_field(ESSERT_PLUGIN_NAME),
                    'User Type' => sanitize_text_field(ESSERT_APP_USER_TYPE)
                ], isset($payload['props']) ? $payload['props'] : [])
            ]
        ]);
    }
}
